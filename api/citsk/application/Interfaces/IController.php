<?php

namespace Citsk\Interfaces;

interface IController
{

    /**
     * @return void
     */
    public function getList(): void;

    /**
     * @return void
     */
    public function add(): void;

    /**
     * @return void
     */
    public function update(): void;

    /**
     * @return void
     */
    public function remove(): void;
}
