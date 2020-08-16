<?php

namespace App\MLB;

use Carbon\Carbon;
use GuzzleHttp\Client;

class API
{
    public const ENDPOINT = 'https://statsapi.mlb.com/api';

    /**
     * The MLB API version to use for the request.
     *
     * @param string
     *   The version string.
     */
    protected $version = 1;

    /**
     * Type modifier used for different requests.
     *
     * @param string
     *   The type string.
     */
    protected $type = null;

    /**
     * The URI to get.
     *
     * @param string
     *   The URI string.
     */
    protected $uri = '';

    /**
     * The from date.
     *
     * @param \Carbon\Carbon
     *   The from date
     */
    protected $from = null;

    /**
     * The to date.
     *
     * @param \Carbon\Carbon
     *   The to date
     */
    protected $to = null;

    public function __construct()
    {
        $this->today();
    }

    public static function get()
    {
        return new static();
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function version($version)
    {
        $this->version = $version;
        return $this;
    }

    public function from(Carbon $from)
    {
        $this->from = $from;
        return $this;
    }

    public function to(Carbon $to)
    {
        $this->to = $to;
        return $this;
    }

    public function today()
    {
        $this->from = now();
        $this->to = $this->from;
        return $this;
    }

    public function teams()
    {
        return $this->uri('/teams?sportId=1')
            ->request()['teams'];
    }

    public function leagues()
    {
        return $this->uri('/league')->request()['leagues'];
    }

    public function divisions()
    {
        return $this->uri('/divisions')->request()['divisions'];
    }

    /**
     * Get player data.
     *
     * @param array $ids
     *   The list of player ids.
     */
    public function players($ids = [])
    {
        $ids = implode(',', $ids);
        $uri = '/people/?personIds=' . $ids . '&hydrate=currentTeam';

        return $this->uri($uri)
            ->request()['people'];
    }

    /**
     * Get an MLB roster.
     *
     * @param string $teamKey
     *   The MLB team key (teamPk)
     */
    public function roster($teamKey)
    {
        if (!$this->type) {
            $this->type('40Man');
        }

        $uri = '/teams/' . $teamKey
            . '/roster/' . $this->type
            . '?date=' . $this->from->format('m/d/Y');

        return $this->uri($uri)
            ->request()['roster'];
    }

    /**
     * Get a specific game.
     *
     * @param string $key
     *   The MLB game key (gamePk)
     */
    public function game($key)
    {
        return $this->uri('/game/' . $key . '/feed/live')
            ->version(1.1)
            ->request();
    }

    public function gameTypes()
    {
        return $this->uri('/gameTypes')->request();
    }

    public function rosterTypes()
    {
        return $this->uri('/rosterTypes')->request();
    }

    public function sports()
    {
        return $this->uri('/sports')->request()['sports'];
    }

    /**
     * Get a specific game's content.
     *
     * @param string $key
     *   The MLB game key (gamePk)
     */
    public function gameContent($key)
    {
        return $this->uri('/game/' . $key . '/content')
            ->request();
    }

    public function games()
    {
        $uri = '/schedule?sportId=1&startDate='
            . $this->from->format('m/d/Y')
            . '&endDate='
            . $this->to->format('m/d/Y');

        if ($this->type) {
            $uri .= '&gameType=' . $this->type;
        }

        return $this->uri($uri)
            ->request();
    }

    public function request()
    {
        $client = new Client();
        $url = self::ENDPOINT . '/v' . $this->version . $this->uri;

        try {
            $res = $client->get($url, [
                'timeout' => 5.0 // wait at most 5 seconds
            ]);
            return json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
