<?php
namespace App;

class AdminExtension
{

    /**
     *
     * @var string[]
     */
    private $extensionsArray = [];

    /**
     *
     * @var string
     */
    private $extensionDir = '';

    /**
     *
     * @param string $extensionDir
     */
    public function __construct(string $extensionDir)
    {
        if (! empty($extensionDir)) {
            $this->extensionDir = $extensionDir;
            $this->loadExtensionArray($this->extensionDir);
        }
    }

    /**
     *
     * @return string[]
     */
    public function getAdminExtensionsRouteYaml(): array
    {
        $result = [];
        foreach ($this->extensionsArray as $folder) {
            $file = "{$this->extensionDir}/{$folder}/router.yml";
            if (file_exists($file)) {
                $result[] = $file;
            }
        }
        return $result;
    }

    /**
     *
     * @param string $extensionBaseDir
     */
    private function loadExtensionArray(string $extensionBaseDir)
    {
        $this->extensionsArray = array_diff(
            scandir($extensionBaseDir),
            ['.', '..']
        );
    }
}
