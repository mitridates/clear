<?php
namespace App\Utils\Json\JsonErrorSerializer;

class JsonError
{
    private string $id;
    private array $links=[];
    private mixed $status;
    private mixed $code;
    private string $title;
    private string $detail;
    /**
     * An object containing references to the source of the error
     */
    public array $source=[];
    /**
     * A 'meta' object containing non-standard meta-information about the error.
     */
    public array $meta=[];

    public function generateId(int $length=5): string
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public function setRelatedLink(string $href, ?array $meta): JsonError
    {
        $this->links['related']=array_filter(array_merge(['href'=>$href], ['meta'=>$meta]));
        return $this;
    }

    public function setSelfLink(string $href): JsonError
    {
        $this->links['self']= $href;
        return $this;
    }

    public function setId(mixed $id=null): JsonError
    {
        $this->id = $id? $id : $this->generateId();
        return $this;
    }

    public function setStatus(mixed $status): JsonError
    {
        $this->status = $status;
        return $this;
    }

    public function setCode(mixed $code): JsonError
    {
        $this->code = $code;
        return $this;
    }

    public function setTitle(string $title): JsonError
    {
        $this->title = $title;
        return $this;
    }

    public function setDetail(string $detail): JsonError
    {
        $this->detail = $detail;
        return $this;
    }

    public function setSource(string $key, string $value): JsonError
    {

        if(in_array($key, ['pointer', 'parameter', 'header'])){
            $this->source[$key]= $value;
        }
        return $this;
    }

    public function setMeta(string $key, mixed $val): JsonError
    {
        $this->meta[$key] = $val;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getSource(): array
    {
        return $this->source;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function toArray(): array
    {
        $ret = [];
        foreach ($this as $k=>$v) {
            $ret[$k]=$v;
        }
        return $ret;
    }
}