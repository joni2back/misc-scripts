<?php

namespace Panel\CommonBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Panel\CommonBundle\Helper\JsonAdapterHelper;

class TranslationController extends BaseController
{

    /**
     * @Route("/translation/fix")
     */
    public function translationFix($old = null, $new = null, $catalog = null)
    {
        $old = $this->getParam('old', get_defined_vars());
        $new = $this->getParam('new', get_defined_vars());
        $catalog = $this->getParam('catalog', get_defined_vars());

        $response = $this->_fix($old, $new, $catalog);

        return $this->prepareOutput($response);
    }

    /**
     * @Route("/translation/spanish/{catalog}", defaults={"catalog"="messages"})
     * @Route("/translation/es/{catalog}", defaults={"catalog"="messages"})
     */
    public function spanishAction($catalog)
    {
        $aTranslations = $this->getTranslations($catalog, 'es');

        $response = new Response();
        $response->headers->set(
            'Content-Type', 'application/json; charset=utf-8'
        );

        $jsonResponse = JsonAdapterHelper::jEncode($aTranslations);
        $response->setContent($jsonResponse);
        return $response;
    }

    /**
     * @Route("/translation/english/{catalog}", defaults={"catalog"="messages"})
     * @Route("/translation/en/{catalog}", defaults={"catalog"="messages"})
     */
    public function englishAction($catalog)
    {
        $aTranslations = $this->getTranslations($catalog, 'en');

        $response = new Response();
        $response->headers->set(
            'Content-Type', 'application/json; charset=utf-8'
        );

        $jsonResponse = JsonAdapterHelper::jEncode($aTranslations);
        $response->setContent($jsonResponse);
        return $response;
    }

    protected function getTranslations($catalog, $locale)
    {
        $cache = $this->get('apc_cache');
        $cacheKey = sprintf('trans__%s_%s', $locale, $catalog);

        if (! $cache->doContains($cacheKey)) {

            $oLanguage = $this->getDoctrine()->getManager()
                ->getRepository('PanelCommonBundle:Language')
                ->findOneBy(array(
                    'locale' => $locale
                ));

            $aCollection = $this->getDoctrine()->getManager()
                ->getRepository('PanelCommonBundle:LanguageTranslation')
                ->findBy(array(
                    'catalogue' => $catalog,
                    'language' => $oLanguage ? $oLanguage->getId() : null,
                ));

            $aTranslations = array();
            foreach ($aCollection as $oTranslation) {
                $key = $oTranslation->getLanguageToken()->getToken();
                $aTranslations[$key] = $oTranslation->getTranslation();
            }
            $cache->doSave($cacheKey, $aTranslations);
        }

        return $cache->doFetch($cacheKey);
    }


    protected function _fix($old, $new, $catalog)
    {
        $oEm = $this->getDoctrine()->getManager();

        $oLangTran = $oEm->getRepository('PanelCommonBundle:LanguageTranslation')->findOneBy(array(
            'catalogue' => $catalog,
            'translation' => $old,
        ));

        if (! $oLangTran) {
            throw new \Panel\CommonBundle\XCutting\Exception\UserException('No se encontro la traduccion');
        }

        $oLangTran->setTranslation($new);
        $oEm->persist($oLangTran);
        $oEm->flush();

        new \Panel\CommonBundle\Helper\PanelExec('sed -i '
            . "s/\"'$old'\"/\"'$new'\"/g  "
            . BASE_DIR . '/data-model/change-master-data/language_translation.sql'
        );

        new \Panel\CommonBundle\Helper\PanelExec('rm -rf  ' . APP_DIR . '/cache/dev/translations');
        new \Panel\CommonBundle\Helper\PanelExec('rm -rf  ' . APP_DIR . '/cache/prod/translations');
        return $oLangTran->toArray();
    }

}