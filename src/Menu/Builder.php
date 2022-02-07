<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class Builder
{    
        private $security;
        private $factory;

        public function __construct(FactoryInterface $factory, Security $security)
        {
                $this->factory = $factory;
                $this->security = $security;
        }

        public function adminMenu(array $options): ItemInterface
        {
                
                $menu = $this->factory->createItem('adminroot', array('childrenAttributes' => array('class' => 'navbar-nav me-auto mb-2 mb-sm-0')))
                        ->setExtra('translation_domain', 'App');

                $menu->addChild('events.events', array('route' => 'event_list'))
                        ->setExtra('translation_domain', 'App');

                $menu->addChild('repertoire.repertoire', array('route' => 'repertoire_list'))
                        ->setExtra('translation_domain', 'App');

                $menu->addChild('videos.videos', array('route' => 'video_list'))
                        ->setExtra('translation_domain', 'App');

                $menu->addChild('gallery.galleries', array('route' => 'gallery_list'))
                        ->setExtra('translation_domain', 'App');

                $menu->addChild('textblock.textblocks', array('route' => 'textblock_list'))
                        ->setExtra('translation_domain', 'App');

                if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
                        $menu->addChild('users.users', array('route' => 'user_list'));
                }

                return $menu;
        }

        public function mainMenu(array $options): ItemInterface
        {
                $menu = $this->factory->createItem('root', array('childrenAttributes' => array('class' => 'vertical large-horizontal menu')));
                
                $menu->addChild('website.agenda', array('route' => 'agenda'))
                        ->setExtra('translation_domain', 'Front');
                $menu->addChild('website.repertoire', array('route' => 'repertoire'))
                        ->setExtra('translation_domain', 'Front');
                $menu->addChild('website.membres', array('route' => 'membres'))
                        ->setExtra('translation_domain', 'Front');
                $menu->addChild('website.photos', array('route' => 'photos'))
                        ->setExtra('translation_domain', 'Front');
                //$menu->addChild('website.videos', array('uri' => 'https://www.youtube.com/channel/UC_tp-m7g0eqs0IwxhA1k9mA'))
                //        ->setLinkAttribute('target', '_blank')
                //        ->setExtra('translation_domain', 'Front');
                $menu->addChild('website.contact', array('route' => 'contact'))
                        ->setExtra('translation_domain', 'Front');
                $menu->addChild('website.joinus', array('route' => 'joinus'))
                        ->setExtra('translation_domain', 'Front');
                
                return $menu;
        }
}