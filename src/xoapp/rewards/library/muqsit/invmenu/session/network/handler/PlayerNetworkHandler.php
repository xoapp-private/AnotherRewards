<?php

declare(strict_types=1);

namespace xoapp\rewards\library\muqsit\invmenu\session\network\handler;

use Closure;
use xoapp\rewards\library\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}