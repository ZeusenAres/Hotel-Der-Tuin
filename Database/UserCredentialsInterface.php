<?php
interface UserCredentialsInterface
{
    public function login(string $username, string $password) : string;
    public function register(string $username, string $email, string $password) : void;
}