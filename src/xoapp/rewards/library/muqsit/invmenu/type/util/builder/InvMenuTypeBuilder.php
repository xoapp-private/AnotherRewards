<?php

declare(strict_types=1);

namespace xoapp\rewards\library\muqsit\invmenu\type\util\builder;

use xoapp\rewards\library\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}