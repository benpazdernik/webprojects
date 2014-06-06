<?php  return array (
  0 => 
  array (
    'text' => 'Nástěnka',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/misc/logo_tbar.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => 'MODx.loadPage(""); return false;',
    'permissions' => 'home',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Nástěnky',
        'parent' => 'dashboard',
        'action' => 53,
        'description' => 'Správa nastavení všech nástěnek.',
        'icon' => 'images/icons/information.png',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'dashboards',
        'controller' => 'system/dashboards',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
    ),
  ),
  1 => 
  array (
    'text' => 'Portál',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/misc/logo_tbar.gif',
    'menuindex' => 1,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_site',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Náhled',
        'parent' => 'site',
        'action' => 0,
        'description' => 'Načtení úvodní stránky do nového okna/záložky prohlížeče.',
        'icon' => 'images/icons/show.gif',
        'menuindex' => 0,
        'params' => '',
        'handler' => 'MODx.preview(); return false;',
        'permissions' => '',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Vyprázdnit Cache',
        'parent' => 'site',
        'action' => 0,
        'description' => 'Vyprázdnění cache celého portálu.',
        'icon' => 'images/icons/refresh.png',
        'menuindex' => 1,
        'params' => '',
        'handler' => 'MODx.clearCache(); return false;',
        'permissions' => 'empty_cache',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Odstranit zámky',
        'parent' => 'site',
        'action' => 0,
        'description' => 'Odstranění zámků ze všech dokumentů a elementů správce obsahu, které vznikly při úpravách ostatními uživateli.',
        'icon' => 'images/ext/default/grid/hmenu-unlock.png',
        'menuindex' => 2,
        'params' => '',
        'handler' => '
MODx.msg.confirm({
    title: _(\'remove_locks\')
    ,text: _(\'confirm_remove_locks\')
    ,url: MODx.config.connectors_url+\'system/remove_locks.php\'
    ,params: {
        action: \'remove\'
    }
    ,listeners: {
        \'success\': {fn:function() { Ext.getCmp("modx-resource-tree").refresh(); },scope:this}
    }
});',
        'permissions' => 'remove_locks',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'Hledání',
        'parent' => 'site',
        'action' => 54,
        'description' => 'Hledat dokument.',
        'icon' => 'images/icons/context_view.gif',
        'menuindex' => 3,
        'params' => '',
        'handler' => '',
        'permissions' => 'search',
        'controller' => 'search',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      4 => 
      array (
        'text' => 'Nový dokument',
        'parent' => 'site',
        'action' => 55,
        'description' => 'Vytvoření nového dokumentu.',
        'icon' => 'images/icons/folder_page_add.png',
        'menuindex' => 4,
        'params' => '',
        'handler' => '',
        'permissions' => 'new_document',
        'controller' => 'resource/create',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      5 => 
      array (
        'text' => 'Nový webový odkaz',
        'parent' => 'site',
        'action' => 55,
        'description' => 'Vytvoření nového webového odkazu na existující URL s přesměrováním.',
        'icon' => 'images/icons/link_add.png',
        'menuindex' => 5,
        'params' => '&class_key=modWebLink',
        'handler' => '',
        'permissions' => 'new_weblink',
        'controller' => 'resource/create',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      6 => 
      array (
        'text' => 'Nový symbolický odkaz',
        'parent' => 'site',
        'action' => 55,
        'description' => 'Vytvoření nového symbolického odkazu na existující URL adresu, bez přesměrování.',
        'icon' => 'images/icons/link_add.png',
        'menuindex' => 6,
        'params' => '&class_key=modSymLink',
        'handler' => '',
        'permissions' => 'new_symlink',
        'controller' => 'resource/create',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      7 => 
      array (
        'text' => 'Nový statický dokument',
        'parent' => 'site',
        'action' => 55,
        'description' => 'Vytvoření nového souborově orientovaného statického dokumentu.',
        'icon' => 'images/icons/link_add.png',
        'menuindex' => 7,
        'params' => '&class_key=modStaticResource',
        'handler' => '',
        'permissions' => 'new_static_resource',
        'controller' => 'resource/create',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      8 => 
      array (
        'text' => 'Odhlásit',
        'parent' => 'site',
        'action' => 0,
        'description' => 'Odhlášení z MODX správce obsahu.',
        'icon' => 'images/misc/logo_tbar.gif',
        'menuindex' => 8,
        'params' => '',
        'handler' => 'MODx.logout(); return false;',
        'permissions' => 'logout',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
    ),
  ),
  2 => 
  array (
    'text' => 'Komponenty',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 2,
    'params' => '',
    'handler' => '',
    'permissions' => 'components',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
    ),
  ),
  3 => 
  array (
    'text' => 'Bezpečnost',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/lock.gif',
    'menuindex' => 3,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_security',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Správa uživatelů',
        'parent' => 'security',
        'action' => 56,
        'description' => 'Možnost přidat, upravit nebo přiřadit oprávnění uživatelům.',
        'icon' => 'images/icons/user.gif',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'view_user',
        'controller' => 'security/user',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Správa přístupů',
        'parent' => 'security',
        'action' => 57,
        'description' => 'Správa uživatelských skupin správce obsahu, jejich rolí a přístupových práv.',
        'icon' => 'images/icons/mnu_users.gif',
        'menuindex' => 1,
        'params' => '',
        'handler' => '',
        'permissions' => 'access_permissions',
        'controller' => 'security/permission',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Skupiny dokumentů',
        'parent' => 'security',
        'action' => 58,
        'description' => 'Správa skupin dokumentů.',
        'icon' => '',
        'menuindex' => 2,
        'params' => '',
        'handler' => '',
        'permissions' => 'access_permissions',
        'controller' => 'security/resourcegroup/index',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'Přizpůsobení formulářů',
        'parent' => 'security',
        'action' => 59,
        'description' => 'Přizpůsobení formulářů správce obsahu.',
        'icon' => 'images/misc/logo_tbar.gif',
        'menuindex' => 3,
        'params' => '',
        'handler' => '',
        'permissions' => 'customize_forms',
        'controller' => 'security/forms',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      4 => 
      array (
        'text' => 'Resetovat přístup',
        'parent' => 'security',
        'action' => 0,
        'description' => 'Resetovat všechny přístupy a znovu načíst cache.',
        'icon' => 'images/icons/unzip.gif',
        'menuindex' => 4,
        'params' => '',
        'handler' => 'MODx.msg.confirm({
    title: _(\'flush_access\')
    ,text: _(\'flush_access_confirm\')
    ,url: MODx.config.connectors_url+\'security/access/index.php\'
    ,params: {
        action: \'flush\'
    }
    ,listeners: {
        \'success\': {fn:function() { location.href = \'./\'; },scope:this}
    }
});',
        'permissions' => 'access_permissions',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      5 => 
      array (
        'text' => 'Resetovat všechny přístupy',
        'parent' => 'security',
        'action' => 0,
        'description' => 'Resetovat všechny session a odhlásit všechny uživatele.',
        'icon' => 'images/icons/unzip.gif',
        'menuindex' => 5,
        'params' => '',
        'handler' => 'MODx.msg.confirm({
    title: _(\'flush_sessions\')
    ,text: _(\'flush_sessions_confirm\')
    ,url: MODx.config.connectors_url+\'security/flush.php\'
    ,params: {
        action: \'flush\'
    }
    ,listeners: {
        \'success\': {fn:function() { location.href = \'./\'; },scope:this}
    }
});',
        'permissions' => 'flush_sessions',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
    ),
  ),
  4 => 
  array (
    'text' => 'Nástroje',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/menu_settings.gif',
    'menuindex' => 4,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_tools',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Importovat dokumenty',
        'parent' => 'tools',
        'action' => 60,
        'description' => 'Dávkové importování statických dokumentů do portálu.',
        'icon' => 'images/icons/application_side_contract.png',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'import_static',
        'controller' => 'system/import',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Importovat HTML',
        'parent' => 'tools',
        'action' => 61,
        'description' => 'Dávkové importování HTML souborů do portálu.',
        'icon' => 'images/icons/application_side_contract.png',
        'menuindex' => 1,
        'params' => '',
        'handler' => '',
        'permissions' => 'import_static',
        'controller' => 'system/import/html',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Sady vlastností',
        'parent' => 'tools',
        'action' => 62,
        'description' => 'Správa všech sad vlastností vašeho portálu a elementů, kterých k němu náleží.',
        'icon' => 'images/misc/logo_tbar.gif',
        'menuindex' => 2,
        'params' => '',
        'handler' => '',
        'permissions' => 'property_sets',
        'controller' => 'element/propertyset/index',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'Zdroje médií',
        'parent' => 'tools',
        'action' => 63,
        'description' => 'Zde můžete spravovat zdroje médií.',
        'icon' => 'images/misc/logo_tbar.gif',
        'menuindex' => 2,
        'params' => '',
        'handler' => '',
        'permissions' => 'sources',
        'controller' => 'source/index',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
    ),
  ),
  5 => 
  array (
    'text' => 'Hlášení',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/menu_settings16.gif',
    'menuindex' => 5,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_reports',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Plán portálu',
        'parent' => 'reports',
        'action' => 64,
        'description' => 'Zobrazit dokumenty s nastavenou nadcházejí publikací nebo ukončením publikace.',
        'icon' => 'images/icons/cal.gif',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'view_document',
        'controller' => 'resource/site_schedule',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Události správce obsahu',
        'parent' => 'reports',
        'action' => 65,
        'description' => 'Zobrazit aktuální aktivitu uživatelů správce obsahu.',
        'icon' => '',
        'menuindex' => 1,
        'params' => '',
        'handler' => '',
        'permissions' => 'logs',
        'controller' => 'system/logs/index',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Chybové zprávy',
        'parent' => 'reports',
        'action' => 66,
        'description' => 'Zobrazení MODX chybových zpráv.',
        'icon' => 'images/icons/comment.gif',
        'menuindex' => 2,
        'params' => '',
        'handler' => '',
        'permissions' => 'view_eventlog',
        'controller' => 'system/event',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'Systemové informace',
        'parent' => 'reports',
        'action' => 67,
        'description' => 'Zobrazení informací o serveru, jako např. phpinfo, info o databázi a dalších.',
        'icon' => 'images/icons/logging.gif',
        'menuindex' => 3,
        'params' => '',
        'handler' => '',
        'permissions' => 'view_sysinfo',
        'controller' => 'system/info',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      4 => 
      array (
        'text' => 'O MODX Revolution',
        'parent' => 'reports',
        'action' => 68,
        'description' => 'Více informací MODX Revolution.',
        'icon' => 'images/icons/information.png',
        'menuindex' => 4,
        'params' => '',
        'handler' => '',
        'permissions' => 'about',
        'controller' => 'help',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
    ),
  ),
  6 => 
  array (
    'text' => 'Systém',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/misc/logo_tbar.gif',
    'menuindex' => 6,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_system',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Správce balíčků',
        'parent' => 'system',
        'action' => 69,
        'description' => 'Stáhování komponent třetích stran, přidávání poskytovatelů a instalace balíčků.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'packages',
        'controller' => 'workspaces',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Konfigurace systému',
        'parent' => 'system',
        'action' => 70,
        'description' => 'Změna nebo vytvoření systémového nastavení v rámci celého portálu.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 1,
        'params' => '',
        'handler' => '',
        'permissions' => 'settings',
        'controller' => 'system/settings',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Správce slovníků',
        'parent' => 'system',
        'action' => 71,
        'description' => 'Úprava všech jazykových záznamů pro MODX.',
        'icon' => 'images/icons/logging.gif',
        'menuindex' => 2,
        'params' => '',
        'handler' => '',
        'permissions' => 'lexicons',
        'controller' => 'workspaces/lexicon',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'Typy obsahů',
        'parent' => 'system',
        'action' => 72,
        'description' => 'Správa typů obsahů pro dokumenty, např. .html, .js, atd.',
        'icon' => 'images/icons/logging.gif',
        'menuindex' => 3,
        'params' => '',
        'handler' => '',
        'permissions' => 'content_types',
        'controller' => 'system/contenttype',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      4 => 
      array (
        'text' => 'Kontexty',
        'parent' => 'system',
        'action' => 73,
        'description' => 'Správa kontextů portálů a jejich nastavení.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 4,
        'params' => '',
        'handler' => '',
        'permissions' => 'view_context',
        'controller' => 'context',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      5 => 
      array (
        'text' => 'Akce',
        'parent' => 'system',
        'action' => 74,
        'description' => 'Správa akcí a struktury horního menu.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 5,
        'params' => '',
        'handler' => '',
        'permissions' => 'menus,actions',
        'controller' => 'system/action',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      6 => 
      array (
        'text' => 'Jmenné prostory',
        'parent' => 'system',
        'action' => 75,
        'description' => 'Zacházení se jmennými prostory, které rozlišují mezi různými přidanými komponentami.',
        'icon' => '',
        'menuindex' => 6,
        'params' => '',
        'handler' => '',
        'permissions' => 'namespaces',
        'controller' => 'workspaces/namespace',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
    ),
  ),
  7 => 
  array (
    'text' => 'Uživatel',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/user_go.png',
    'menuindex' => 7,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_user',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Profil',
        'parent' => 'user',
        'action' => 76,
        'description' => 'Možnost úpravy svého osobního profilu.',
        'icon' => '',
        'menuindex' => 0,
        'params' => '',
        'handler' => '',
        'permissions' => 'change_profile',
        'controller' => 'security/profile',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Zprávy',
        'parent' => 'user',
        'action' => 77,
        'description' => 'Zobrazení zpráv a možnost jejich poslání ostatním uživatelům.',
        'icon' => 'images/icons/messages.gif',
        'menuindex' => 1,
        'params' => '',
        'handler' => '',
        'permissions' => 'messages',
        'controller' => 'security/message',
        'namespace' => 'core',
        'children' => 
        array (
        ),
      ),
    ),
  ),
  8 => 
  array (
    'text' => 'Podpora',
    'parent' => '',
    'action' => 0,
    'description' => '',
    'icon' => 'images/icons/sysinfo.gif',
    'menuindex' => 8,
    'params' => '',
    'handler' => '',
    'permissions' => 'menu_support',
    'controller' => '',
    'namespace' => NULL,
    'children' => 
    array (
      0 => 
      array (
        'text' => 'Diskuse',
        'parent' => 'support',
        'action' => 0,
        'description' => 'Zobrazit oficiální MODX diskusi.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 0,
        'params' => '',
        'handler' => 'window.open("http://modx.com/forums");',
        'permissions' => '',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      1 => 
      array (
        'text' => 'Wiki',
        'parent' => 'support',
        'action' => 0,
        'description' => 'Spustit oficiální MODX dokumentaci.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 1,
        'params' => '',
        'handler' => 'window.open("http://rtfm.modx.com/");',
        'permissions' => '',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      2 => 
      array (
        'text' => 'Chyby',
        'parent' => 'support',
        'action' => 0,
        'description' => 'Spustit MODX bugtracker pro sledování chyb.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 2,
        'params' => '',
        'handler' => 'window.open("http://bugs.modx.com/projects/revo/issues");',
        'permissions' => '',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
      3 => 
      array (
        'text' => 'API dokumentace',
        'parent' => 'support',
        'action' => 0,
        'description' => 'Kompletní API dokumentace pro MODX Revolution.',
        'icon' => 'images/icons/sysinfo.gif',
        'menuindex' => 3,
        'params' => '',
        'handler' => 'window.open("http://api.modx.com/revolution/2.2/");',
        'permissions' => '',
        'controller' => '',
        'namespace' => NULL,
        'children' => 
        array (
        ),
      ),
    ),
  ),
);