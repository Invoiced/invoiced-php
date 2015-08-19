<?php

namespace Invoiced;

class Transaction extends Object
{
	use Operations\Create;
	use Operations\All;
	use Operations\Update;
	use Operations\Delete;
}