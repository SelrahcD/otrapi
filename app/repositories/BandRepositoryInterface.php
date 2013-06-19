<?php

interface BandRepositoryInterface {

	public function make($attributes = array());

	public function store(Band $band);

	public function get($id);

	public function isUserMember(Band $band, User $user);

	public function addMember(Band $band, User $user);
}