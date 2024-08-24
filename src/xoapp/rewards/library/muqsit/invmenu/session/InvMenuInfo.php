<?php

declare(strict_types=1);

namespace xoapp\rewards\library\muqsit\invmenu\session;

use xoapp\rewards\library\muqsit\invmenu\InvMenu;
use xoapp\rewards\library\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}