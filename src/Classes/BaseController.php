<?php

namespace MTC\Payments\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MTC\Payments\Traits\SetVariables;
use MTC\Payments\Traits\SetRequiredFields;

class BaseController 
{
	use SetVariables,SetRequiredFields;
}