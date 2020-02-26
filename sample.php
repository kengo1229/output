<?php

class Man
{
	public  $name;

	function __construct()	{
		$this->name = "鈴木";
	}
	function show() {
		echo $this->name;
	}
}

$seito = new Man();
$seito->show();
 ?>
