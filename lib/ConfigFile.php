<?php

/*
 * Human Friendly Config.
 *
 * @author     Xeloses (https://github.com/Xeloses)
 * @package    HumanFriendlyConfig (https://github.com/Xeloses/human-friendly-config)
 * @version    1.0.1
 * @copyright  Xeloses 2019-2020
 * @license    MIT (http://en.wikipedia.org/wiki/MIT_License)
 */

namespace Xeloses\HumanFriendlyConfig;

use Xeloses\HumanFriendlyConfig\Exceptions\ConfigFileException;

/**
 * ConfigFile class
 *
 * @package HumanFriendlyConfig
 *
 * @property string {CONFIG_PARAM_NAME}
 *
 * @method get(string $name, ?mixed $default)
 * @method save()
 * @method saveAs(string $new_file_name)
 */
class ConfigFile{
    /**
     * Config data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Config file path.
     *
     * @var string
     */
    protected $filename;

    /**
     * Constructor.
     *
     * @param string $file_name
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $file_name)
    {
        if(empty($file_name))
        {
            throw new \InvalidArgumentException('Config file name required.');
        }
        elseif(!is_file($file_name))
        {
            throw new \InvalidArgumentException('Invalid config file name (or file is unavailable).');
        }

        $this->filename = $file_name;
        $f = trim(file_get_contents($file_name));
        if(!empty($f))
        {
            foreach(explode("\n",$f) as $line)
            {
                $line = trim($line);
                if(!empty($line) && !preg_match('/^(\/\/|\#).*/',$line))
                {
                    $parsed = explode('=',$line);
                    $name  = preg_replace('/[\s]+/','_',strtolower(trim(array_shift($parsed))));
                    $value = count($parsed) ? trim(implode('=',$parsed)) : null;
                    $this->data[$name] = $value;
                }
            }
        }
    }

    /**
     * Get value from config.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if(empty($this->data) || !array_key_exists($name,$this->data))
        {
            return $default;
        }

        return $this->{$name};
    }

    /**
     * Save values to file.
     *
     * @return void
     *
     * @throws ConfigFileException
     */
    public function save(): void
    {
        $data = '';
        foreach($this->data as $key => $value)
        {
            $data .= ucfirst(str_replace('_',' ',$key)).' = '.$value.PHP_EOL;
        }

        if(!file_put_contents($this->filename,trim($data)))
        {
            throw new ConfigFileException('Error attempt to save config to file.');
        }
    }

    /**
     * Save values to file.
     *
     * @param string $new_file_name
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws ConfigFileException
     */
    public function saveAs(string $new_file_name): void
    {
        if(empty($new_file_name))
        {
            throw new \InvalidArgumentException('File name required.');
        }
        if(is_file($new_file_name))
        {
            throw new ConfigFileException('Could not save config to a new file (file already exists).');
        }

        $this->filename = $new_file_name;
        $this->save();
    }

    /**
     * Handles dynamic get calls to the object.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if(empty($this->data) || !array_key_exists($name,$this->data))
        {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * Handles dynamic set calls to the object.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Handles dynamic attempts to convert object to string.
     *
     * @return string
     */
    public function __toString()
    {
        return empty($this->data)?'[]':json_encode($this->data,JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
?>
