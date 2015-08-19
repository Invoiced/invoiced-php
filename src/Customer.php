<?php

namespace Invoiced;

class Customer extends Object
{
	use Operations\Create;
	use Operations\All;
	use Operations\Update;
	use Operations\Delete;
}