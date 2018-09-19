<?php
/**
 * Created by PhpStorm.
 * User: Matt
 * Date: 20/04/2016
 * Time: 2:32 PM
 */

namespace Freshdesk;

use Freshdesk\Exceptions\AccessDeniedException;
use Freshdesk\Exceptions\ApiException;
use Freshdesk\Exceptions\AuthenticationException;
use Freshdesk\Exceptions\ConflictingStateException;
use Freshdesk\Exceptions\RateLimitExceededException;
use Freshdesk\Exceptions\UnsupportedContentTypeException;
use Freshdesk\Resources\Agent;
use Freshdesk\Resources\BusinessHour;
use Freshdesk\Resources\Category;
use Freshdesk\Resources\Comment;
use Freshdesk\Resources\Company;
use Freshdesk\Resources\Contact;
use Freshdesk\Resources\Conversation;
use Freshdesk\Resources\EmailConfig;
use Freshdesk\Resources\Forum;
use Freshdesk\Resources\Group;
use Freshdesk\Resources\Product;
use Freshdesk\Resources\SLAPolicy;
use Freshdesk\Resources\Ticket;
use Freshdesk\Resources\TimeEntry;
use Freshdesk\Resources\Topic;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class for interacting with the Freshdesk Api
 *
 * This is the only class that should be instantiated directly. All API resources are available
 * via the relevant public properties
 *
 * @package Api
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class Api
{
    /**
     * Agent resources
     *
     * @api
     * @var Agent
     */
    public $agents;

    /**
     * Company resources
     *
     * @api
     * @var Company
     */
    public $companies;

    /**
     * Contact resources
     *
     * @api
     * @var Contact
     */
    public $contacts;

    /**
     * Group resources
     *
     * @api
     * @var Group
     */
    public $groups;

    /**
     * Ticket resources
     *
     * @api
     * @var Ticket
     */
    public $tickets;

    /**
     * TimeEntry resources
     *
     * @api
     * @var TimeEntry
     */
    public $timeEntries;

    /**
     * Conversation resources
     *
     * @api
     * @var Conversation
     */
    public $conversations;

    /**
     * Category resources
     *
     * @api
     * @var Category
     */
    public $categories;

    /**
     * Forum resources
     *
     * @api
     * @var Forum
     */
    public $forums;

    /**
     * Topic resources
     *
     * @api
     * @var Topic
     */
    public $topics;

    /**
     * Comment resources
     *
     * @api
     * @var Comment
     */
    public $comments;

    //Admin

    /**
     * Email Config resources
     *
     * @api
     * @var EmailConfig
     */
    public $emailConfigs;

    /**
     * Access Product resources
     *
     * @api
     * @var Product
     */
    public $products;

    /**
     * Business Hours resources
     *
     * @api
     * @var BusinessHour
     */
    public $businessHours;

    /**
     * SLA Policy resources
     *
     * @api
     * @var SLAPolicy
     */
    public $slaPolicies;

    /**
     * @internal
     * @var Client
     */
    protected $client;

    /**
     * @internal
     * @var string
     */
    private $baseUrl;

    /**
     * Constructs a new api instance
     *
     * @api
     * @param string $apiKey
     * @param string $domain
     * @throws Exceptions\InvalidConfigurationException
     */
    public function __construct($apiKey, $domain)
    {
        $this->validateConstructorArgs($apiKey, $domain);

        $this->baseUrl = sprintf('https://%s.freshdesk.com/api/v2', $domain);

        $this->client = new Client([
                'auth' => [$apiKey, 'X']
            ]
        );

        $this->setupResources();
    }


    /**
     * Internal method for handling requests
     *
     * @internal
     * @param $method
     * @param $endpoint
     * @param array|null $data
     * @param array|null $query
     * @return mixed|null
     * @throws ApiException
     * @throws ConflictingStateException
     * @throws RateLimitExceededException
     * @throws UnsupportedContentTypeException
     */
    public function request($method, $endpoint, array $data = null, array $query = null)
    {
        $key = 'json';
        if ($this->hasAttachments($data)) {
            $data = $this->formatDataForMultipart($data);
            $key = 'multipart';
        }
        $options = [$key => $data];

        if (isset($query)) {
            $options['query'] = $query;
        }

        $url = $this->baseUrl . $endpoint;

        return $this->performRequest($method, $url, $options);
    }

    /**
     * Checks for attachments in the $data payload
     *
     * @internal
     *
     * @param $data
     */
    private function hasAttachments($data)
    {
        return (isset($data['attachments']) && count($data['attachments']) > 0);
    }


    /**
     * Formats the data into a Guzzle Mulitpart request format
     *
     * @internal
     *
     * @param $data
     */
    private function formatDataForMultipart($data)
    {
        $flat = [];

        $this->flattern($flat, $data);

        $multipartData = [];
        foreach ($flat as $key => $value) {
            $multi = [
                'name' => sprintf('%s', $value['key']),
                'contents' => $value['value'],
            ];

            if (strpos($value['key'], 'attachments[]') !== false) {
                $multi['contents'] = fopen($value['value'], 'r+');
                $multi['filename'] = basename($value['value']);
            }

            $multipartData[] = $multi;
        }
        return $multipartData;
    }

    private function flattern(&$flat, $array, $baseKey = [])
    {
        $isFirstLevel = count($flat) === 0;
        if (is_array($array)) {
            if ($this->isAssoc($array)) {
                foreach ($array as $k => $v) {
                    $newBaseKey = $baseKey;
                    $newBaseKey[] = sprintf($isFirstLevel ? '%s' : '[%s]', $k);
                    $this->recursive($flat, $v, $newBaseKey);
                }
            } else {
                foreach ($array as $k => $v) {
                    $newBaseKey = $baseKey;
                    $newBaseKey[] = '[]';
                    $this->recursive($flat, $v, $newBaseKey);
                }
            }
        } else {
            $flat[] = [
                'key' => implode('', $baseKey),
                'value' => $array,
            ];
        }
    }

    private function isAssoc(array $arr)
    {

        if (array() === $arr) {
            return false;
        }
        ksort($arr);
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Performs the request
     *
     * @internal
     *
     * @param $method
     * @param $url
     * @param $options
     * @return mixed|null
     * @throws AccessDeniedException
     * @throws ApiException
     * @throws AuthenticationException
     * @throws ConflictingStateException
     */
    private function performRequest($method, $url, $options) {

        try {
            switch ($method) {
                case 'GET':
                    return json_decode($this->client->get($url, $options)->getBody(), true);
                case 'POST':
                    return json_decode($this->client->post($url, $options)->getBody(), true);
                case 'PUT':
                    return json_decode($this->client->put($url, $options)->getBody(), true);
                case 'DELETE':
                    return json_decode($this->client->delete($url, $options)->getBody(), true);
                default:
                    return null;
            }
        } catch (RequestException $e) {
\Log::debug('options!', $options);
\Log::debug((string) $e->getResponse()->getBody());
abort(500);
            throw ApiException::create($e);
        }
    }


    /**
     * @param $apiKey
     * @param $domain
     * @throws Exceptions\InvalidConfigurationException
     * @internal
     *
     */
    private function validateConstructorArgs($apiKey, $domain)
    {
        if (!isset($apiKey)) {
            throw new Exceptions\InvalidConfigurationException("API key is empty.");
        }

        if (!isset($domain)) {
            throw new Exceptions\InvalidConfigurationException("Domain is empty.");
        }
    }

    /**
     * @internal
     */
    private function setupResources()
    {
        //People
        $this->agents = new Agent($this);
        $this->companies = new Company($this);
        $this->contacts = new Contact($this);
        $this->groups = new Group($this);

        //Tickets
        $this->tickets = new Ticket($this);
        $this->timeEntries = new TimeEntry($this);
        $this->conversations = new Conversation($this);

        //Discussions
        $this->categories = new Category($this);
        $this->forums = new Forum($this);
        $this->topics = new Topic($this);
        $this->comments = new Comment($this);

        //Admin
        $this->products = new Product($this);
        $this->emailConfigs = new EmailConfig($this);
        $this->slaPolicies = new SLAPolicy($this);
        $this->businessHours = new BusinessHour($this);
    }
}
