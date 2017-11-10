<?php
namespace Tattler\Base;
/** @var \Skeleton\Base\IBoneConstructor $this */


use Tattler\DAL\TattlerAccessDAO;
use Tattler\Modules\TattlerModule;


$this->set(DAL\ITattlerAccessDAO::class,	TattlerAccessDAO::class);
$this->set(Modules\ITattlerModule::class,	TattlerModule::class);
