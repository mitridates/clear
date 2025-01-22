<?php
namespace App\Twig;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
/**
 * Current page parameters
 *
 * @author mitridates
 */
class JsonExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var array
     */
    protected $suggest= [];


    /**
     * @param UrlGeneratorInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(UrlGeneratorInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator= $translator;
    }

    /**
     * Valid only to nested select country->[adminX|area]
     * @todo mod & test to country->admin2, admin1->admin2, admin1->area...
     * @param string $type
     * @param string|null $child
     * @return array
     */
    public function dataRepopulate(string $type, ?string $child= null, ?string $parameters= null): array
    {
        $ret=[];
        //child selects
        if($child){
            $ret["data-child"] = $child;
        }else{
            $ret["data-populatable"]='true';//Javascript use, options can be removed/replaced
        }
       if($parameters) $ret["data-parameters"]= $parameters;

        switch ($type) {
            case 'country':
                $ret= ['attr'=>array_merge($ret,
                    [
                        "data-repopulate" => 'country',//document.querySelectorAll('[data-repopulate]')
                        "data-cache" => 'true'//cache responses by parameters
                    ])];
                break;
            case 'admin1':
                $ret= ['attr'=>array_merge($ret, [
                    'data-url'=> $this->router->generate('json_geonames_admin1_children'),
                    'data-parentid'=> 'countryid',
                    //placeholder es necesario cuando se vacía el select
                    'data-placeholder'=> $this->translator->trans('select.government.level.admin1',[],'cavemessages')
                ])];
                break;
            case 'admin2':
                $ret= ['attr'=>array_merge($ret, [
                    'data-url'=> $this->router->generate('json_geonames_admin2_children'),
                    'data-parentid'=> 'admin1id',
                    'data-placeholder'=> $this->translator->trans('select.government.level.admin2',[],'cavemessages')
                ])];
                break;
            case 'admin3':
                $ret= ['attr'=>array_merge($ret, [
                    'data-url'=> $this->router->generate('json_geonames_admin3_children'),
                    'data-parentid'=> 'admin2id',
                    'data-placeholder'=> $this->translator->trans('select.government.level.admin3',[],'cavemessages')
                ])];
                break;
            case 'area':
                $ret= ['attr'=>array_merge($ret, [
                    'data-url'=> $this->router->generate('json_geonames_area_children'),
                    'data-parentid'=> 'code',//Country code(ex.: ES) || Admin1 code (ex.: ES.30)
                    'data-placeholder'=> $this->translator->trans('select.area',[],'cavemessages'),
                    'data-attributes'=> 'id,name,comment'//option id, text, title
                ])];
                break;
        }

        return $ret;
    }

    /**
     * @param string $name Suggest name
     * @return array

     */
    public function dataSuggest(string $name): array
    {
        if(in_array($name, $this->suggest)) return $this->suggest[$name];
        $ret= [
            "data-noResults" => $this->translator->trans('suggest.noResults', [], 'cavemessages'),
            "data-searchPlaceholder" => $this->translator->trans('suggest.searchPlaceholder', [], 'cavemessages'),
            'data-path'=> null,
            'data-placeholder'=> null
        ];
        switch ($name){
            case 'link':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_link'),
                    'data-placeholder'=> $this->translator->trans('select.link', [], 'cavemessages'),
                    'data-spec-type'=> 'link'
                ]);
                break;
            case 'mimetype':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_mime'),
                    'data-placeholder'=> $this->translator->trans('select.mimetype', [], 'cavemessages'),
                    'data-spec-type'=> 'mimetype'
                ]);
                break;
            case 'citation':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_citation'),
                    'data-placeholder'=> $this->translator->trans('select.citation', [], 'cavemessages'),
                    'data-spec-type'=> 'citation'
                ]);
                break;

            case 'article'://@todo remove...
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_article'),
                    'data-placeholder'=> $this->translator->trans('select.article', [], 'cavemessages'),
                    'data-spec-type'=> 'article'
                ]);
                break;
            case 'area':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_area'),
                    'data-placeholder'=> $this->translator->trans('select.area', [], 'cavemessages'),
                    'data-spec-type'=> 'area'
                ]);
                break;
            case 'mapserie':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_mapserie'),
                    'data-placeholder'=> $this->translator->trans('select.mapserie', [], 'cavemessages'),
                    'data-spec-type'=> 'mapserie'
                ]);
                break;
            case 'map':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_map'),
                    'data-placeholder'=> $this->translator->trans('select.map', [], 'cavemessages'),
                    'data-spec-type'=> 'map'
                ]);
                break;
            case 'organisation':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_organisation'),
                    'data-placeholder'=> $this->translator->trans('select.organisation', [], 'cavemessages'),
                    'data-spec-type'=> 'organisation'
                ]);
                break;
            case 'person':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_person'),
                    'data-placeholder'=> $this->translator->trans('select.person', [], 'cavemessages'),
                    'data-spec-type'=> 'person'
                ]);
                break;
            case 'fielddefinition':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_fielddefinition'),
                    'data-placeholder'=> $this->translator->trans('select.fielddefinition', [], 'cavemessages'),
                    'data-spec-type'=> 'fielddefinition'
                ]);
                break;
            case 'specie':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_specie'),
                    'data-placeholder'=> $this->translator->trans('select.specie', [], 'cavemessages'),
                    'data-spec-type'=> 'specie'
                ]);
                break;
            case 'cave':
                $ret= array_merge($ret, [
                    'data-path'=> $this->router->generate('json_autocomplete_cave'),
                    'data-placeholder'=> $this->translator->trans('select.cave', [], 'cavemessages'),
                    'data-spec-type'=> 'cave'
                ]);
                break;
        }
        return $this->suggest[$name]= ['attr'=>$ret];
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('dataSuggest',array($this, 'dataSuggest')),
            new TwigFunction('dataRepopulate',array($this, 'dataRepopulate')),
        );
    }
}