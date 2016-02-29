<?php
namespace AdminController\Menu;

use FrontController\Controller as FrontController;

class AjaxLoadController extends FrontController
{

    public function run()
    {
        $alias = $this->view()->getRoute('alias');
        $page = $this->view()->getRoute('page');

        if ($alias != 'category') {
            $content = $this->selectPostOrPage($alias);
        } else { // $page as categoryId
            $content = $this->selectCategory($page);
        }

        $array = [];
        foreach ($content as $row) {
            $rowArray = [];
            $rowArray['id'] = $row->getId();
            $rowArray['title'] = $row->getTitle();
            $rowArray['link'] = "{$alias}/{$row->getAlias()}";
            $rowArray['alias'] = $alias;
            if ($alias == 'post') {
                $rowArray['category'] = $row->getCategory()->getTitle();
            }
            $array[] = $rowArray;
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($array);
        exit();
    }

    /**
     *
     * @return array
     */
    private function selectCategory(int $catId): array
    {
        $this->query()->setQuery('category')
            ->id($catId)
            ->limit(1);
        return $this->query()->getStaticQuery();
    }

    /**
     *
     * @return array
     */
    private function selectPostOrPage(string $alias): array
    {
        if (empty(@$_POST['search'])) {
            $this->query()->setQuery($alias);
        } else {
            $this->query()->setQuery($alias)
                ->titleLike($_POST['search']);
        }
        return $this->query()->getStaticQuery();
    }
}
