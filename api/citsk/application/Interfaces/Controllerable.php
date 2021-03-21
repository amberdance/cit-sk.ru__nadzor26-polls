<?php

namespace Citsk\Interfaces;

interface Controllerable
{
    /**
     * @return void
     */
    public function initializeController(): void;

    /**
     * @return array|null
     */
    public function callRequestedMethod(): ?array;

    /**
     * @param object|null $data
     *
     * @return void
     */
    public function dataResponse(?object $data): void;

    /**
     * @param array|null $data
     * @param int $status
     *
     * @return void
     */
    public function successResponse(?array $data = null, int $status = 1): void;

    /**
     * @param int $status
     * @param string|null $errorMessage
     *
     * @return void
     */
    public function errorResponse(int $status = 0, ?string $errorMessage = null): void;

}
