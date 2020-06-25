<?php
namespace SIMONKOEHLER\Slug\Controller;
use SIMONKOEHLER\Slug\Utility\HelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\JsonResponse;

/*
 * This file was created by Simon Köhler
 * https://simon-koehler.com
 */

class AjaxController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var HelperUtility
     */
    protected $helper;

    /**
     * function ajaxList
     *
     * @return void
     */
    public function ajaxList(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $output = [];
        $queryParams = $request->getQueryParams();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($queryParams['table']);
        $queryBuilder->getRestrictions()->removeAll();
        $result = $queryBuilder
            ->select('*')
            ->from($queryParams['table'])
            ->execute();
        while ($row = $result->fetch()) {
            $output[] = $row;
        }
        return new JsonResponse($output);
    }

    /**
     * function savePageSlug
     *
     * @return void
     */
    public function savePageSlug(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $this->helper = GeneralUtility::makeInstance(HelperUtility::class);
        $slug = $this->helper->returnUniqueSlug('page', $queryParams['slug'], $queryParams['uid'], 'pages', 'slug');
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder
            ->update('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($queryParams['uid'],\PDO::PARAM_INT))
            )
            ->set('slug',$slug) // Function "createNamedParameter" is NOT needed here!
            ->execute();

        if($statement){
            $responseInfo['status'] = '1';
            $responseInfo['slug'] = $slug;
        }
        else{
            $responseInfo['status'] = '0';
            $responseInfo['slug'] = $slug;
        }
        return new JsonResponse($responseInfo);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function saveRecordSlug(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $uid = $queryParams['uid'];
        $table = $queryParams['table'];
        $slug = $queryParams['slug'];
        $slugField  = $queryParams['slugField'];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid,\PDO::PARAM_INT))
            )
            ->set($slugField,$slug) // Function "createNamedParameter" is NOT needed here!
            ->execute();
        $responseInfo['status'] = $statement;
        $responseInfo['slug'] = $slug;
        return new JsonResponse($responseInfo);
    }

    /**
     * function slugExists
     *
     * @return void
     */
    public function slugExists(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();
        $result = $queryBuilder
            ->count('slug')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter($queryParams['slug']))
            )
            ->execute()
            ->fetchColumn(0);
        return new HtmlResponse($result);
    }

    /**
     * function generatePageSlug
     *
     * @return void
     */
    public function generatePageSlug(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $fieldConfig = $GLOBALS['TCA']['pages']['columns']['slug']['config'];
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, 'pages', 'slug', $fieldConfig);
        $this->helper = GeneralUtility::makeInstance(HelperUtility::class);
        $queryParams = $request->getQueryParams();
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($queryParams['uid'],\PDO::PARAM_INT))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            $slugGenerated = $slugHelper->generate($row, $row['pid']);
            break;
        }
        $slug = $this->helper->returnUniqueSlug('page', $slugGenerated, $row['uid'], 'pages', 'slug');
        $responseInfo['status'] = $statement;
        $responseInfo['slug'] = $slug;
        return new JsonResponse($responseInfo);
    }

    /**
     * function generateRecordSlug
     *
     * @return void
     */
    public function generateRecordSlug(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $uid = $queryParams['uid'];
        $table = $queryParams['table'];
        $slugField  = $queryParams['slugField'];
        $titleField  = $queryParams['titleField'];

        $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$slugField]['config'];
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, $table, $slugField, $fieldConfig);
        $this->helper = GeneralUtility::makeInstance(HelperUtility::class);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($queryParams['uid'],\PDO::PARAM_INT))
            )
            ->execute();
        while ($row = $statement->fetch()) {
            $slugGenerated = $slugHelper->sanitize($row[$titleField]);
            break;
        }

        $responseInfo['status'] = $statement;
        $responseInfo['slug'] = $slugGenerated;
        return new JsonResponse($responseInfo);
    }

    /**
     * function loadTreeItemSlugs
     *
     * @return void
     */
    public function loadTreeItemSlugs(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        $this->helper = GeneralUtility::makeInstance(HelperUtility::class);
        $translations = $this->helper->getPageTranslationsByUid($queryParams['uid']);
        $root = BackendUtility::getRecord('pages',$queryParams['uid']);
        $languages = $this->helper->getLanguages();
        $html .= '<div class="well">';
        $html .= '<h2>'.$root['title'].' <small>'.$root['seo_title'].'</small></h2>';
        $html .= '<div class="input-group">'
                . '<span class="input-group-addon"><i class="fa fa-globe"></i></span>'
                . '<input type="text" data-uid="'.$root['uid'].'" value="'.$root['slug'].'" class="form-control slug-input page-'.$root['uid'].'">'
                . '<span class="input-group-btn"><button data-uid="'.$root['uid'].'" id="savePageSlug-'.$root['uid'].'" class="btn btn-default savePageSlug" title="Save slug"><i class="fa fa-save"></i></button></span>'
                . '</div>';
        foreach ($translations as $page) {
            foreach ($languages as $value) {
                if($value['uid'] === $page['sys_language_uid']){
                    $icon = $value['language_isocode'];
                }
            }
            $html .= '<h3>'.$page['title'].' <small>'.$page['seo_title'].'</small></h3>';
            $html .= '<div class="input-group">'
                . '<span class="input-group-addon">'.$icon.'</span>'
                . '<input type="text" data-uid="'.$page['uid'].'" value="'.$page['slug'].'" class="form-control slug-input page-'.$page['uid'].'">'
                . '<span class="input-group-btn"><button data-uid="'.$page['uid'].'" id="savePageSlug-'.$page['uid'].'" class="btn btn-default savePageSlug" title="Save slug"><i class="fa fa-save"></i></button></span>'
                . '</div>';
        }
        $html .= '</div>';
        return new HtmlResponse($html);
    }

}
