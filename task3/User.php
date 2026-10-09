<?php
class User {
    public $name;
    public $email;
    public $mobile;
    public $governorate;
    public $track;
    public $skills;
    public $message;

    public function __construct($name, $email, $mobile, $governorate, $track, $skills, $message) {
        $this->name = $name;
        $this->email = $email;
        $this->mobile = $mobile;
        $this->governorate = $governorate;
        $this->track = $track;
        $this->skills = $skills;
        $this->message = $message;
    }
}