<?php

namespace App\MLB;

use Carbon\Carbon;
use GuzzleHttp\Client;

class API
{
    public const ENDPOINT = 'https://statsapi.mlb.com/api';

    /**
     *  The MLB API version to use for the request.
     *
     *  @var string
     */
    protected $version = 1;

    /**
     *  Type modifier used for different requests.
     *
     *  @var string
     */
    protected $type = null;

    /**
     *  The URI to get.
     *
     *  @var string
     */
    protected $uri = '';

    /**
     *  The from date.
     *
     *  @var \Carbon\Carbon
     */
    protected $from = null;

    /**
     *  The to date.
     *
     *  @var \Carbon\Carbon
     */
    protected $to = null;

    /**
     *  Hydration list.
     *
     *  @var array
     */
    protected $hydrate;

    /**
     *  Params.
     *
     *  @var array
     */
    protected $params;

    public function __construct()
    {
        $this->today();
        $this->params = [];
        $this->hydrate = [];
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

    public function hydrate($hydrate)
    {
        if (!is_array($hydrate)) {
            $this->hydrate[] = $hydrate;
        } else {
            $this->hydrate = array_merge($hydrate, $this->hydrate);
        }

        return $this;
    }

    public function params($params)
    {
        $this->params = array_merge($params, $this->params);
        return $this;
    }

    public function teams()
    {
        return $this->params(['sportId' => 1])
            ->basic('teams');
    }

    public function leagues()
    {
        return $this->uri('/league')->request()['leagues'];
    }

    public function divisions()
    {
        return $this->basic('divisions');
    }

    public function venues()
    {
        return $this->basic('venues');
    }

    public function sports()
    {
        return $this->basic('sports');
    }

    protected function basic($thing)
    {
        return $this->uri('/' . $thing)->request()[$thing];
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

        return $this->uri('/people')
            ->params(['personIds' => $ids])
            ->hydrate('currentTeam')
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

        return $this->uri('/teams/' . $teamKey . '/roster/' . $this->type)
            ->params(['date' => $this->from->format('m/d/Y')])
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

    public function schedule()
    {
        $this->params([
            'sportId' => 1,
            'startDate' => $this->from->format('m/d/Y'),
            'endDate' => $this->to->format('m/d/Y'),
        ]);

        if ($this->type) {
            $this->params(['gameType' => $this->type]);
        }

        return $this->uri('/schedule')->request();
    }

    public function request()
    {
        $client = new Client();
        $url = self::ENDPOINT . '/v' . $this->version . $this->uri;

        if ($this->hydrate) {
            $this->params(['hydrate', implode(',', $this->hydrate)]);
        }
        if ($this->params) {
            $url .= '?' . implode('&', $this->params);
        }

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
