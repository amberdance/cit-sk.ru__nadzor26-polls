<?php

namespace Citsk\Models\Structure;

class CommonStructure extends Structure
{

    /**
     * @param array $rows
     *
     * @return void
     */
    protected function AbstractStructure(array $rows): void
    {

        foreach ($rows as $row) {
            $this->structure[] = [
                "id"    => (int) $row['id'],
                "label" => $row['label'] ?? $row['value'] ?? $row['title'] ?? null,
            ];
        }
    }
}
