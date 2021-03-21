<?php
namespace Citsk\Models\Structure;

use Exception;

class Structure
{

    /**
     * @var array
     */
    public $structure = [];

    /**
     * @param array|null $data
     * @param string|null $label
     * @param null $params
     */
    public function __construct(?array $data, string $label = null, $params = null)
    {
        if (!$data) {
            return [];
        }

        $methodName = $this->getMethodName($label);
        $this->handleStructure($methodName, $data, $params);

    }

    /**
     * @param string $methodName
     * @param array $data
     * @param mixed $params
     *
     * @return array
     */
    private function handleStructure(string $methodName, array $data, $params): array
    {

        if (!$data) {
            $this->structure = [];
        }

        call_user_func([$this, $methodName], $data, $params);

        return $this->structure;

    }

    /**
     * @param null ?string
     *
     * @return string
     */
    private function getMethodName(?string $label = null): string
    {

        $classMethods = get_class_methods($this);

        if (count($classMethods) == 4) {
            return $classMethods[0];
        }

        if (!$label) {
            return 'AbstractStructure';
        }

        global $USER;

        $methodName = null;

        // if ($USER->isAuthorized) {
        //     $methodName = str_replace("the", "theAdmin", $label);
        // }

        if (method_exists($this, $methodName)) {
            return $methodName;

        } else {
            if (method_exists($this, $label)) {
                return $label;
            }
        }

        throw new Exception("Structure not found");
    }
}
