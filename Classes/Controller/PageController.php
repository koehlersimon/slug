<?php
namespace SIMONKOEHLER\Slug\Controller;
use SIMONKOEHLER\Slug\Utility\HelperUtility;
use SIMONKOEHLER\Slug\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file was created by Simon Köhler
 * https://simon-koehler.com
 */

class PageController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    public $pageRepository;
    protected $iconFactory;
    protected $helper;
    protected $languages;
    protected $sites;
    protected $backendConfiguration;

    /**
    * @param PageRepository $pageRepository
    */
    public function __construct(PageRepository $pageRepository) {
         $this->pageRepository = $pageRepository;
         $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
         $this->helper = GeneralUtility::makeInstance(HelperUtility::class);
         $this->backendConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('slug');
         $this->sites = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites();
    }

    /**
     * Generate an Ajax List // EXPERIMENTAL!
     */
    protected function ajaxListAction()
    {

        // Set the order by options for fluid viewhelper f:form.switch
        $filterOptions['orderby'] = [
            ['value' => 'crdate', 'label' => $this->helper->getLangKey('filter.form.select.option.creation_date')],
            ['value' => 'tstamp', 'label' => $this->helper->getLangKey('filter.form.select.option.tstamp')],
            ['value' => 'title', 'label' => $this->helper->getLangKey('filter.form.select.option.title')],
            ['value' => 'slug', 'label' => $this->helper->getLangKey('filter.form.select.option.slug')],
            ['value' => 'sys_language_uid', 'label' => $this->helper->getLangKey('filter.form.select.option.sys_language_uid')],
            ['value' => 'is_siteroot', 'label' => $this->helper->getLangKey('filter.form.select.option.is_siteroot')],
            ['value' => 'doktype', 'label' => $this->helper->getLangKey('filter.form.select.option.doktype')]
        ];

        $filterOptions['order'] = [
            ['value' => 'DESC', 'label' => $this->helper->getLangKey('filter.form.select.option.descending')],
            ['value' => 'ASC', 'label' => $this->helper->getLangKey('filter.form.select.option.ascending')]
        ];

        $filterOptions['maxentries'] = [
            ['value' => '5', 'label' => '5'],
            ['value' => '10', 'label' => '10'],
            ['value' => '20', 'label' => '20'],
            ['value' => '30', 'label' => '30'],
            ['value' => '40', 'label' => '40'],
            ['value' => '50', 'label' => '50'],
            ['value' => '60', 'label' => '60'],
            ['value' => '70', 'label' => '70'],
            ['value' => '80', 'label' => '80'],
            ['value' => '90', 'label' => '90'],
            ['value' => '100', 'label' => '100'],
            ['value' => '150', 'label' => '150'],
            ['value' => '200', 'label' => '200'],
            ['value' => '300', 'label' => '300'],
            ['value' => '400', 'label' => '400'],
            ['value' => '500', 'label' => '500'],
            ['value' => '1000', 'label' => '1000 (be careful!)'],
            ['value' => '1500', 'label' => '1500'],
            ['value' => '2000', 'label' => '2000'],
            ['value' => '3000', 'label' => '3000'],
            ['value' => '4000', 'label' => '4000'],
            ['value' => '5000', 'label' => '5000']
        ];

        // Check if slugpro is loaded
        if(ExtensionManagementUtility::isLoaded('slugpro')){
            $slugpro = [
                'version' => '1.0.0'
            ];
        }
        else{
            $slugpro = FALSE;
        }

        // Assign variables to the view
        $this->view->assignMultiple([
            'backendConfiguration' => $this->backendConfiguration,
            'extEmconf' => $this->helper->getEmConfiguration('slug'),
            'filterOptions' => $filterOptions,
            'sites' => (array) $this->sites,
            'slugpro' => $slugpro
        ]);
    }

    /**
     * List all slugs from the pages table
     */
    protected function listAction()
    {

        // Check if filter variables are available, otherwise set default values from ExtensionConfiguration
        if($this->request->hasArgument('filter')){
            $filterVariables = $this->request->getArgument('filter');
            $filterVariables['pointer'] = 0;
        }
        else{
            $filterVariables['maxentries'] = $this->backendConfiguration['defaultMaxEntries'];
            $filterVariables['orderby'] = $this->backendConfiguration['defaultOrderBy'];
            $filterVariables['order'] = $this->backendConfiguration['defaultOrder'];
            $filterVariables['status'] = $this->backendConfiguration['defaultStatus'];
            $filterVariables['key'] = '';
            $filterVariables['pointer'] = 0;
        }

        // Set the order by options for fluid viewhelper f:form.switch
        $filterOptions['orderby'] = [
            ['value' => 'crdate', 'label' => $this->helper->getLangKey('filter.form.select.option.creation_date')],
            ['value' => 'tstamp', 'label' => $this->helper->getLangKey('filter.form.select.option.tstamp')],
            ['value' => 'title', 'label' => $this->helper->getLangKey('filter.form.select.option.title')],
            ['value' => 'slug', 'label' => $this->helper->getLangKey('filter.form.select.option.slug')],
            ['value' => 'sys_language_uid', 'label' => $this->helper->getLangKey('filter.form.select.option.sys_language_uid')],
            ['value' => 'is_siteroot', 'label' => $this->helper->getLangKey('filter.form.select.option.is_siteroot')],
            ['value' => 'doktype', 'label' => $this->helper->getLangKey('filter.form.select.option.doktype')]
        ];

        $filterOptions['order'] = [
            ['value' => 'DESC', 'label' => $this->helper->getLangKey('filter.form.select.option.descending')],
            ['value' => 'ASC', 'label' => $this->helper->getLangKey('filter.form.select.option.ascending')]
        ];

        $filterOptions['maxentries'] = [
            ['value' => '10', 'label' => '10'],
            ['value' => '20', 'label' => '20'],
            ['value' => '30', 'label' => '30'],
            ['value' => '40', 'label' => '40'],
            ['value' => '50', 'label' => '50'],
            ['value' => '60', 'label' => '60'],
            ['value' => '70', 'label' => '70'],
            ['value' => '80', 'label' => '80'],
            ['value' => '90', 'label' => '90'],
            ['value' => '100', 'label' => '100'],
            ['value' => '150', 'label' => '150'],
            ['value' => '200', 'label' => '200'],
            ['value' => '300', 'label' => '300'],
            ['value' => '400', 'label' => '400'],
            ['value' => '500', 'label' => '500'],
            ['value' => '1000', 'label' => '1000 (be careful!)'],
            ['value' => '1500', 'label' => '1500'],
            ['value' => '2000', 'label' => '2000'],
            ['value' => '3000', 'label' => '3000']
        ];

        $pages = $this->pageRepository->findAllFiltered($filterVariables);

        $arrayPaginator = new ArrayPaginator($pages, 1, 8);
        $pagination = new SimplePagination($arrayPaginator);

        $this->view->assignMultiple([
            'pages' => $pages,
            'paginator' => $arrayPaginator,
            'pagination' => $pagination,
            'pagesArray' => range(1, $pagination->getLastPageNumber()),
            'filter' => $filterVariables,
            'backendConfiguration' => $this->backendConfiguration,
            'beLanguage' => $GLOBALS['BE_USER']->user['lang'],
            'extEmconf' => $this->helper->getEmConfiguration('slug'),
            'filterOptions' => $filterOptions,
            'additionalTables' => $this->settings['additionalTables'],
            'totalPages' => $this->pageRepository->findTotalPages()
        ]);

    }


    /**
     * Generate a tree view
     */
    protected function treeAction()
    {

        if($this->request->hasArgument('siteRoot')){
            $siteRoot = $this->request->getArgument('siteRoot');
        }
        else{
            $siteRoot = $this->backendConfiguration['treeDefaultRoot'];
        }

        if(!$siteRoot || $siteRoot === 0){
            //Get the first existing site in the root level and its uid
            foreach ($this->sites as $site) {
                $siteRoot = $site->getRootPageId();
                break;
            }
        }

        if($this->request->hasArgument('depth')){
            $depth = $this->request->getArgument('depth');
        }
        else{
            $depth = $this->backendConfiguration['treeDefaultDepth'];
        }

        if($siteRoot){
            $args['siteRoot'] = $siteRoot;
            $args['depth'] = $depth;
            $siteRootRecord = BackendUtility::getRecord('pages',$siteRoot);
            $tree = GeneralUtility::makeInstance(PageTreeView::class);
            $tree->init('AND ' . $GLOBALS['BE_USER']->getPagePermsClause(1));
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
            $icon = '<span class="page-icon">' . $iconFactory->getIconForRecord('pages', $siteRootRecord, Icon::SIZE_SMALL)->render() . '</span>';
            $tree->tree[] = array(
                'row' => $siteRootRecord,
                'icon' => $icon
            );
            $tree->getTree($siteRoot,$depth,'');
            $this->view->assignMultiple([
                'tree' => $tree->tree,
                'backendConfiguration' => $this->backendConfiguration,
                'extEmconf' => $this->helper->getEmConfiguration('slug'),
                'sites' => (array) $this->sites,
                'args' => $args
            ]);
        }
        else{
            //$this->addFlashMessage('Error: No Site root found! PageController.php Line 130');
        }

    }

}
