<?php

interface SqlDb {
	public function connect();
	public function query(string $query);
	public function getConnection();
	public function isConnected() : bool;
	public function getQueriesNumber();
	public function getVersion();
	public function getDbName();
}