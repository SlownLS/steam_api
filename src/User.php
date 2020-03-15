<?php

namespace SlownLS\Steam;

if( session_status() == PHP_SESSION_NONE ){
    session_start();
}

class User{
    use Traits\Login;
    use Traits\Util;

    /**
     * Get info from user session
     *
     * @param string $key
     * @return string
     */
    protected function GetLocalInfo(string $key) : string
    {
        if( !$this->IsLogged() ){ return "Not logged!"; }

        return $_SESSION["user"]->$key;
    }

    /**
     * Get infos to user
     *
     * @param string $steamid
     * @return object
     */
    protected function GetInfos(string $steamid) : object
    {
        if( filter_var($steamid, FILTER_VALIDATE_URL) ){
            $url = rtrim($steamid,"/");
            $ptn = "/^https?:\/\/steamcommunity\.com\/[a-zA-Z]+\/([a-zA-Z0-9]+)$/";

            preg_match($ptn, $url, $matches);

            if( !isset($matches[1]) ){
                throw new Exception("User not found");
            }
            
            $steamid = $matches[1];
        }

        $isSteamID = $this->IsSteamID($steamid);
        $client = $this->Client();

        if( $isSteamID == false ){
            $response = $client->request("GET", sprintf($this->urlVanity, $this->config["apiKey"], $steamid) );
            $data = $this->Parse($response)->response;

            if( !isset($data->steamid) ){
                throw new Exception("User not found");
            }

            $steamid = $data->steamid;
        }

        $response = $client->request("GET", sprintf( $this->urlUserInfos, $this->config["apiKey"], $steamid) );
        $data = $this->Parse($response);

        if( !isset($data->response->players[0]) ){
            throw new Exception("User not found");
        }

        $data = $data->response->players[0];

        $this->ParseData($data);

        return $data;
    }

    protected function GetFriends(string $steamid) : object
    {
        if( !$this->IsSteamID($steamid) ){ return (object) []; }

        $client = $this->Client();

        $response = $client->request("GET", sprintf( $this->urlFriends, $this->config["apiKey"], $steamid) );
        $status = $response->getStatusCode();

        if( $status != 200 ){
            throw new Exception("Invalid SteamID64");
        }

        $data = $this->Parse($response);    

        return $data;
    }

    protected function GetGames(string $steamid) : object
    {
        if( !$this->IsSteamID($steamid) ){ return (object) []; }

        $client = $this->Client();

        $response = $client->request("GET", sprintf( $this->urlGames, $this->config["apiKey"], $steamid) );
        $status = $response->getStatusCode();

        if( $status != 200 ){
            throw new Exception("Invalid SteamID64");
        }

        $data = $this->Parse($response);    

        var_dump($data);
        exit();
    }
}