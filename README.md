# Steam API
A simple php library for Steam API

## Installation 
To install this library use composer

composer require "slownls/steam_api"

## User functions

~~~ PHP
User::IsLogged() // Whether the user is logged in.

User::IsReturned() // Used to see if the player has logged in with steam

User::Login() // Used to redirect the user to the steam login page.

User::Auth() // Used to log the user in.

User::Logout() // Used to log the user out.

User::GetLocalInfo(string $key) // Used to retrieve information in the session

User::GetInfos(string $urlOrSteamid) // Used to retrieve the information of a steam user

User::GetFriends(string $steamid) // Used to retrieve a user's friends

User::GetGames(string $steamid) // Used to retrieve a user's games
~~~
