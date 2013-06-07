<?php

interface BandRepositoryInterface {

	public function make($attributes = array());

	public function store(Band $band);
}