<?php
namespace SIMONKOEHLER\Slug\Domain\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

/*
 * This file was created by Simon Köhler
 * https://simon-koehler.com
 */

class PageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    protected $table = 'pages';
    protected $fieldName = 'slug';
    protected $languages;

    public function findAllFiltered($filterVariables) {
        $this->languages = $this->getLanguages();
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder
           ->getRestrictions()
           ->removeAll();

        $query = $queryBuilder
            ->select('*')
            ->from('pages')
            ->orderBy($filterVariables['orderby'],$filterVariables['order']);

        switch ($filterVariables['status']) {

            // All pages
            case 'all':
                // simply add nothing to the query...
            break;

            // Only hidden
            case 'hidden':
                $query->andWhere(
                    $queryBuilder->expr()->eq('hidden', 1),
                    $queryBuilder->expr()->eq('deleted', 0)
                );
            break;

            // Only deleted
            case 'deleted':
                $query->where(
                    $queryBuilder->expr()->eq('deleted', 1)
                );
            break;

            // Only visible pages (default)
            default:
                $query->where(
                    $queryBuilder->expr()->eq('hidden', 0),
                    $queryBuilder->expr()->eq('deleted', 0)
                );
            break;
        }

        if($filterVariables['key']){
            $query->andWhere(
                $queryBuilder->expr()->like('slug',$queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards($filterVariables['key']) . '%'))
            );
        }

        $sitefinder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(SiteFinder::class);
        $statement = $query->execute();
        $output = array();

        while ($row = $statement->fetch()) {

            $row['flag'] = $this->getLanguageValue('flag',$row['sys_language_uid']);
            $row['isocode'] = $this->getLanguageValue('language_isocode',$row['sys_language_uid']);

            // If page is a translated page, set l10n_parent as PageUid
            if($row['l10n_parent'] > 0){
                $pageUid = $row['l10n_parent'];
            }
            else{
                $pageUid = $row['uid'];
            }

            // Try to get the Site configuration
            try {
                $site = $sitefinder->getSiteByPageId($pageUid);
                $siteConf = $site->getConfiguration();

                $row['site'] = $site;
                $row['hasSite'] = true;

                // Remove slash from base URL if neccessary
                if(substr($siteConf['base'], -1) === "/"){
                    $row['pageurl'] = substr($siteConf['base'], 0, -1);
                }
                else{
                    $row['pageurl'] = $siteConf['base'];
                }

                if($row['isocode']){
                    $row['pageurl'] = $row['pageurl'].'/'.$row['isocode'];
                }
            }
            catch (SiteNotFoundException $e) {
               $row['hasSite'] = false;
               $row['pageurl'] = '(N/A)';
            }

            array_push($output, $row);

        }
        return $output;
    }

    function getLanguageValue($field,$uid){
        $output = '';
        if (!empty($this->languages)) {
            foreach ($this->languages as $language) {
                if($language['uid'] === $uid){
                    $output = $language[$field];
                    break;
                }
            }
        } else {
            // if we dont have additional languages
            // lets assume we are on the default language
            if($field === 'flag'){
                $output = 'multiple';
            }
        }
        return $output;
    }

    public function getLanguages(){
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $statement = $queryBuilder
            ->select('*')
            ->from('sys_language')
            ->execute();
        $output = array();
        while ($row = $statement->fetch()) {
            array_push($output, $row);
        }
        return $output;
    }

}
