<?php
namespace App\Shared;

/**
 * Simple paginator class
 *
 * @author mitridates
 */
class Paginator
{

    private int $totalPages;

    private int $itemsPerPage;

    private int $totalRows;

    private array $limits;

    private int $offset;

    private int $limit;

    private int $currentPage;

    private float|int $currentPageRows;

    public function __construct(int $currentPage, int $itemsPerPage, int $totalRows)
    {
        $this->totalRows = $totalRows;
        $this->itemsPerPage = $itemsPerPage > 0 ? $itemsPerPage : 20;
        $this->currentPage = $currentPage;
        $this->totalPages = $this->setTotalPages($this->totalRows, $this->itemsPerPage);
        if ($this->totalRows === 0) {
            $start = $end = 0;
        } elseif ($currentPage > $this->totalPages) {
            $start = ($this->totalPages - 1) * $this->itemsPerPage;
            $end = $this->itemsPerPage;
            $this->currentPage = $this->totalPages;
        } elseif ($currentPage > 1 && $currentPage <= $this->totalPages) {
            $start = ($currentPage - 1) * $itemsPerPage;
            $end = $this->itemsPerPage;
        } else {//first page
            $start = 0;
            $end = $this->itemsPerPage;
        }

        $this->limit= $end;
        $this->offset= $start;

        $this->limits = array($start, $end);
        $this->setPageRows();
    }

    /**
     * Set number of pages in paginator
     */
    private function setTotalPages(int $totalRows, int $itemsPerPage): int
    {
        if ($itemsPerPage == 0) {
            $itemsPerPage = 20;/*default*/
        }

        $this->totalPages = intval(ceil($totalRows / $itemsPerPage));
        return $this->totalPages;
    }

    /**
     * Items per page may vary in the last page
     * @return $this
     */
    private function setPageRows(): Paginator
    {
        $max = $this->currentPage * $this->itemsPerPage;
        if ($max > $this->totalRows) {
            $this->currentPageRows = $this->totalRows - (($this->currentPage - 1) * $this->itemsPerPage);
        } else {
            $this->currentPageRows = $this->itemsPerPage;
        }
        return $this;
    }

    public function getPageRows():int
    {
        return $this->currentPageRows;
    }

    /**
     * Pages in paginator
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Num Rows to paginate
     */
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    /**
     * Get current page
     */
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get query limits [offset, limit]
     */
    public function getLimits(): array
    {
        return [$this->offset, $this->limit];
    }

    /**
     * Get offset (first result row)
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get limit (max results rows)
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Get properties array
     * @return array
     */
    public function toArray():array
    {
        return get_object_vars($this);
    }
}