<?php

class Person{
	
	private $uid;
	private $userName;
	private $lastLoginDate;
	private $firstName;
    private $middleInitial;
	private $lastName;
	private $title;
	private $institution;
	private $department;
	private $address;
	private $city;
	private $state;
	private $zip;
	private $country;
	private $phone;
	private $email;
	private $url;
	private $biography;
	private $isPublic = 0;
	private $password;
	private $userTaxonomy = array();

	public  function __construct(){
	}
	
	public function getUid(){
		return $this->uid;
	} 
	
	public function setUid($id): void
	{
		if(is_numeric($id)){
			$this->uid = $id;
		}
	} 
	
	public function getUserName(){
		return $this->cleanOutStr($this->userName);
	}
	
	public function setUserName($idName): void
	{
		if($idName) {
			$this->userName = trim($idName);
		}
	}
	
	public function getFirstName(){
		return $this->cleanOutStr($this->firstName);
	}
	
	public function setFirstName($firstName): void
	{
		if($firstName) {
			$this->firstName = trim($firstName);
		}
	}

    public function getMiddleInitial(){
        return $this->cleanOutStr($this->middleInitial);
    }

    public function setMiddleInitial($middleInitial): void
	{
        if($middleInitial) {
			$this->middleInitial = trim($middleInitial);
		}
    }
	
	public function getLastName(){
		return $this->cleanOutStr($this->lastName);
	}
	
	public function setLastName($lastName): void
	{
		if($lastName) {
			$this->lastName = trim($lastName);
		}
	}
	
	public function getDepartment(){
		return $this->cleanOutStr($this->department);
	}
	
	public function setDepartment($department): void
	{
		if($department) {
			$this->department = trim($department);
		}
	}
	
	public function getTitle(){
		return $this->cleanOutStr($this->title);
	}
	
	public function setTitle($title): void
	{
		if($title) {
			$this->title = trim($title);
		}
	}
	
	public function getInstitution(){
		return $this->cleanOutStr($this->institution);
	}
	
	public function setInstitution($institution): void
	{
		if($institution) {
			$this->institution = trim($institution);
		}
	}
	
	public function getAddress(){
		return $this->cleanOutStr($this->address);
	}
	
	public function setAddress($address): void
	{
		if($address) {
			$this->address = trim($address);
		}
	}
	
	public function getCity(){
		return $this->cleanOutStr($this->city);
	}
	
	public function setCity($city): void
	{
		if($city) {
			$this->city = trim($city);
		}
	}

	public function getState(){
		return $this->cleanOutStr($this->state);
	}
	
	public function setState($state): void
	{
		if($state) {
			$this->state = trim($state);
		}
	}
	
	public function getCountry(){
		return $this->cleanOutStr($this->country);
	}
	
	public function setCountry($country): void
	{
		if($country) {
			$this->country = trim($country);
		}
	}
	
	public function getZip(){
		return $this->cleanOutStr($this->zip);
	}
	
	public function setZip($zip): void
	{
		if($zip) {
			$this->zip = trim($zip);
		}
	}
	
	public function getPhone (){
		return $this->cleanOutStr($this->phone);
	}
	
	public function setPhone($phone): void
	{
		if($phone) {
			$this->phone = trim($phone);
		}
	}
	
	public function getUrl(){
		return $this->cleanOutStr($this->url);
	}
	
	public function setUrl($url): void
	{
		if($url) {
			$this->url = trim($url);
		}
	}
	
	public function getBiography(){
		return $this->cleanOutStr($this->biography);
	}
	
	public function setBiography($bio): void
	{
		if($bio) {
			$this->biography = trim($bio);
		}
	}

	public function getUserTaxonomy($cat = ''){
		if($cat){
			return $this->userTaxonomy[$cat] ?? null;
		}
		return $this->userTaxonomy;
	}

	public function getIsPublic(): bool
	{
		return $this->isPublic === 1;
	}
	
	public function setIsPublic($isPub): void
	{
		$this->isPublic = $isPub;
	}
	
	public function getEmail(){
		return $this->cleanOutStr($this->email);
	}
	
	public function setEmail($email): void
	{
		if($email) {
			$this->email = trim($email);
		}
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function setPassword($password): void
	{
		$this->password = $password;
	}
	
	public function getLastLoginDate(){
		return $this->lastLoginDate;
	}
	
	public function setLastLoginDate($loginDate): void
	{
		$this->lastLoginDate = $loginDate;
	}
	
	private function cleanOutStr($str){
		return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
	}
}
