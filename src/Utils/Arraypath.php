<?php
namespace App\Utils;
/**
 * Permite obtener de un array un valor o parte del array utilizando como separador los dos puntos (colon)
 * @example
 * <pre>
 * $router=[
 *    'root'=>[
 *        'branch'=>[
 *            'leaf'=>'value'
 *        ]
 *    ]
 * ];
 *
 * $path= new Arraypath($router);
 *
 * $path->get('root:branch')//return [leaf=>value]
 * $path->get('root:branch:leaf')//return value
 * </pre>
 * @author mitridates
 */
class Arraypath
{
    /**
     * @var array
     */
    private $Xarr;

    /**
     * @var string
     */
    protected $delimiter;

    public function __construct(array $arr = [], string $delimiter=':')
    {
        $this->Xarr = $arr;
        $this->delimiter= $delimiter;
    }


    /**
     * Crea/actualiza un par clave/valor en un array.<br>
     * $name notation "root:branch:leaf"<br>
     * con el que podemos:
     * - Crear nueva clave/s y su valor: (router:newKey:newKey, newValue)
     * - Sobreescribir clave: (root:branch:keyToOverwrite, newValue)
     *
     * @param string $name notation "root:branch:leaf"
     * @param mixed $value New value
     * @return $this
     */
    public function set(string $name, mixed $value): Arraypath
    {
            if (strrpos($name, $this->delimiter) !== FALSE) {

                $name = trim($name, $this->delimiter);//remove all leading and trailing slashes

                $parts = explode($this->delimiter, $name); //extract parts of the path

                $r = &$this->Xarr; //use current array as the initial value

                $key = end($parts);//last element of string

                foreach ($parts as $part) {//recorremos el array

                    if ($key != $part) {

                        if (isset($r[$part]))
                        {
                            $r =& $r[$part];//move the internal pointer to the end of the array

                        } else {

                            $r[$part] = array();//creamos nueva clave
                            $r = &$r[$part];

                        }
                    }
//                    else {/*path: final y actual  son iguales, algo mas?*/}
                }
                $r[$key] = $value;
            } else {
                $this->Xarr[$name] = $value;
            }

        return $this;
    }

    /**
     * Xpath search
     *
     * @param string $path xpath style notation "root:branch:leaf"
     * @param mixed|null $default Default return value
     * @return null|mixed Found element or default
     */
    public function get(string $path, mixed $default = null): mixed
    {

        if (strrpos($path, $this->delimiter) !== FALSE) //loop if delimiter
        {

            $path = trim($path, $this->delimiter);//remove all leading and trailing slashes

            $value = $this->Xarr;//use current array as the initial value

            $parts = explode($this->delimiter, $path);//extract parts of the path

            foreach ($parts as $part)//loop through each part and lookup key
            {
                if (isset($value[$part])) {

                    $value = $value[$part];//replace value with child

                } else {

                    return $default;//key doesn't exist
                }
            }
            return $value;
        }

        return (array_key_exists($path, $this->Xarr)) ? $this->Xarr[$path] : $default;//if string, lookup key
    }

    /**
     * Return the current array
     * @return array
     */
    public function copy(): array
    {
        return $this->Xarr;
    }

    /**
     * Return partial array as ArrayPath
     * @param string $path
     * @return Arraypath
     */
    public function getPartial(string $path): ArrayPath
    {
        return new Arraypath($this->get($path), $this->delimiter);
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter(string $delimiter): Arraypath
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function join(string $separator, array $paths ): string
    {
        $ret='';
        foreach ($paths as $path) {
            $ret.= $separator.$this->get($path);
        }
        return $ret;
    }
}
