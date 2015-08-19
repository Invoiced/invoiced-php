<?php

namespace Invoiced;

class Invoice extends Object
{
	use Operations\Create;
	use Operations\All;
	use Operations\Update;
	use Operations\Delete;
}