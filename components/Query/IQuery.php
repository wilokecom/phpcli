<?php


#namespace WilokeTest;


interface IQuery
{
	public function setQueryArgs(array $aArgs): IQuery;

	public function setResponse(IResponse $oResponse): IQuery;

	public function getQuery(): \WP_Query;

	public function query(): array;
}
