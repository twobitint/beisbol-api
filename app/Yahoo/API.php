<?php

namespace App\Yahoo;

use App\League;
use App\User;
use Exception;
use GuzzleHttp\Client as Guzzle;

class API
{
    public static $baseUrl = 'https://fantasysports.yahooapis.com/fantasy/v2';

    public static function leagues($token)
    {
        $uri = '/users;use_login=1/games;game_keys=' . env('YAHOO_MLB_GAME_KEY') . '/leagues';

        $response = self::request($token, $uri);

        $leagues = $response->users->user->games->game->leagues->league;

        return collect($leagues)->filter(function ($league) {
            return $league->scoring_type == 'point';
        });
    }

    public static function teams($token, $leagueKey)
    {
        $uri = '/league/' . $leagueKey . '/teams';

        $response = self::request($token, $uri);

        $teams = $response->league->teams->team;

        return $teams;
    }

    public static function teamsWithRosters($token, $leagueKey, $week = 1)
    {
        $uri = '/league/' . $leagueKey . '/teams/roster';

        //$uri .= ';week=' . $week;

        $response = self::request($token, $uri);

        $teams = $response->league->teams->team;

        return $teams;
    }

    public static function request($token, $uri = '/')
    {
        $guzzle = new Guzzle([
            'headers' => [
                'User-Agent' => config('app.user-agent'),
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $url = self::$baseUrl . $uri;

        $response = $guzzle->request('get', $url);

        try {
            return json_decode(json_encode(simplexml_load_string($response->getBody())));
            //return json_decode($response->getBody());
        } catch (Exception $e) {
            return null;
        }
    }

    public static function refreshAuth($user)
    {
        if (!$user->refresh_token) {
            return false;
        }

        try {
            $guzzle = new Guzzle();
            $response = $guzzle->request('post', 'https://api.login.yahoo.com/oauth2/get_token', [
                'form_params' => [
                    'client_id' => config('services.yahoo.client_id'),
                    'client_secret' => config('services.yahoo.client_secret'),
                    'redirect_uri' => 'oob',
                    'refresh_token' => $user->refresh_token,
                    'grant_type' => 'refresh_token',
                ]
            ]);

            $data = json_decode($response->getBody());

            $user->token = $data->access_token;
            $user->refresh_token = $data->refresh_token;
            $user->expires_at = now()->addSeconds($data->expires_in);
            $user->save();
            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}
