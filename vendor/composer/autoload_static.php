<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit99513a915d290fd93000398b104730c7
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'I' => 
        array (
            'Inpsyde\\Modularity\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Inpsyde\\Modularity\\' => 
        array (
            0 => __DIR__ . '/..' . '/inpsyde/modularity/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Inpsyde\\Modularity\\Container\\ContainerConfigurator' => __DIR__ . '/..' . '/inpsyde/modularity/src/Container/ContainerConfigurator.php',
        'Inpsyde\\Modularity\\Container\\PackageProxyContainer' => __DIR__ . '/..' . '/inpsyde/modularity/src/Container/PackageProxyContainer.php',
        'Inpsyde\\Modularity\\Container\\ReadOnlyContainer' => __DIR__ . '/..' . '/inpsyde/modularity/src/Container/ReadOnlyContainer.php',
        'Inpsyde\\Modularity\\Container\\ServiceExtensions' => __DIR__ . '/..' . '/inpsyde/modularity/src/Container/ServiceExtensions.php',
        'Inpsyde\\Modularity\\Module\\ExecutableModule' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/ExecutableModule.php',
        'Inpsyde\\Modularity\\Module\\ExtendingModule' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/ExtendingModule.php',
        'Inpsyde\\Modularity\\Module\\FactoryModule' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/FactoryModule.php',
        'Inpsyde\\Modularity\\Module\\Module' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/Module.php',
        'Inpsyde\\Modularity\\Module\\ModuleClassNameIdTrait' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/ModuleClassNameIdTrait.php',
        'Inpsyde\\Modularity\\Module\\ServiceModule' => __DIR__ . '/..' . '/inpsyde/modularity/src/Module/ServiceModule.php',
        'Inpsyde\\Modularity\\Package' => __DIR__ . '/..' . '/inpsyde/modularity/src/Package.php',
        'Inpsyde\\Modularity\\Properties\\BaseProperties' => __DIR__ . '/..' . '/inpsyde/modularity/src/Properties/BaseProperties.php',
        'Inpsyde\\Modularity\\Properties\\LibraryProperties' => __DIR__ . '/..' . '/inpsyde/modularity/src/Properties/LibraryProperties.php',
        'Inpsyde\\Modularity\\Properties\\PluginProperties' => __DIR__ . '/..' . '/inpsyde/modularity/src/Properties/PluginProperties.php',
        'Inpsyde\\Modularity\\Properties\\Properties' => __DIR__ . '/..' . '/inpsyde/modularity/src/Properties/Properties.php',
        'Inpsyde\\Modularity\\Properties\\ThemeProperties' => __DIR__ . '/..' . '/inpsyde/modularity/src/Properties/ThemeProperties.php',
        'Nevamiss\\Application\\Application_Module' => __DIR__ . '/../..' . '/src/application/class-application-module.php',
        'Nevamiss\\Application\\Assets' => __DIR__ . '/../..' . '/src/application/class-assets.php',
        'Nevamiss\\Application\\Compatibility\\Version_Dependency_Provider' => __DIR__ . '/../..' . '/src/application/compatibility/class-version-dependency-provider.php',
        'Nevamiss\\Application\\Compatibility\\Versions_Dependency_Interface' => __DIR__ . '/../..' . '/src/application/compatibility/versions-dependency-interface.php',
        'Nevamiss\\Application\\DB' => __DIR__ . '/../..' . '/src/application/class-db.php',
        'Nevamiss\\Application\\Not_Found_Exception' => __DIR__ . '/../..' . '/src/application/class-not-found-exception.php',
        'Nevamiss\\Application\\Post_Query\\Query' => __DIR__ . '/../..' . '/src/application/post-query/class-query.php',
        'Nevamiss\\Application\\Setup' => __DIR__ . '/../..' . '/src/application/class-setup.php',
        'Nevamiss\\Application\\Task' => __DIR__ . '/../..' . '/src/application/class-task.php',
        'Nevamiss\\Application\\Uninstall' => __DIR__ . '/../..' . '/src/application/class-uninstall.php',
        'Nevamiss\\Domain\\Contracts\\Create_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/create-interface.php',
        'Nevamiss\\Domain\\Contracts\\Delete_All_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/delete-all-interface.php',
        'Nevamiss\\Domain\\Contracts\\Delete_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/delete-interface.php',
        'Nevamiss\\Domain\\Contracts\\Get_All_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/get-all-interface.php',
        'Nevamiss\\Domain\\Contracts\\Get_One_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/get-one-interface.php',
        'Nevamiss\\Domain\\Contracts\\Update_Interface' => __DIR__ . '/../..' . '/src/domain/contracts/update-interface.php',
        'Nevamiss\\Domain\\DTO\\Share_Response' => __DIR__ . '/../..' . '/src/domain/dto/class-share-response.php',
        'Nevamiss\\Domain\\Entities\\Log' => __DIR__ . '/../..' . '/src/domain/entities/class-log.php',
        'Nevamiss\\Domain\\Entities\\Network_Account' => __DIR__ . '/../..' . '/src/domain/entities/class-network-account.php',
        'Nevamiss\\Domain\\Entities\\Schedule' => __DIR__ . '/../..' . '/src/domain/entities/class-schedule.php',
        'Nevamiss\\Domain\\Entities\\Schedule_Queue' => __DIR__ . '/../..' . '/src/domain/entities/class-schedule-queue.php',
        'Nevamiss\\Domain\\Entities\\Stats' => __DIR__ . '/../..' . '/src/domain/entities/class-stat.php',
        'Nevamiss\\Domain\\Entities\\Task' => __DIR__ . '/../..' . '/src/domain/entities/class-task.php',
        'Nevamiss\\Domain\\Factory\\Factory' => __DIR__ . '/../..' . '/src/domain/factory/class-factory.php',
        'Nevamiss\\Domain\\Repositories\\Command_Query' => __DIR__ . '/../..' . '/src/domain/repositories/class-command-query.php',
        'Nevamiss\\Domain\\Repositories\\Count_Model_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-count-model.php',
        'Nevamiss\\Domain\\Repositories\\Create_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-create.php',
        'Nevamiss\\Domain\\Repositories\\Delete_All_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-delete-all.php',
        'Nevamiss\\Domain\\Repositories\\Delete_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-delete.php',
        'Nevamiss\\Domain\\Repositories\\Get_All_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-get-all.php',
        'Nevamiss\\Domain\\Repositories\\Get_One_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-get-one.php',
        'Nevamiss\\Domain\\Repositories\\Logger_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-logger-repository.php',
        'Nevamiss\\Domain\\Repositories\\Network_Account_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-network-account-repository.php',
        'Nevamiss\\Domain\\Repositories\\Posts_Stats_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-posts-stats-repository.php',
        'Nevamiss\\Domain\\Repositories\\Repository_Common_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-repository-common.php',
        'Nevamiss\\Domain\\Repositories\\Schedule_Queue_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-schedule-queue-repository.php',
        'Nevamiss\\Domain\\Repositories\\Schedule_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-schedule-repository.php',
        'Nevamiss\\Domain\\Repositories\\Task_Repository' => __DIR__ . '/../..' . '/src/domain/repositories/class-task-repository.php',
        'Nevamiss\\Domain\\Repositories\\To_Model_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-to-model.php',
        'Nevamiss\\Domain\\Repositories\\Update_Trait' => __DIR__ . '/../..' . '/src/domain/repositories/trait-update.php',
        'Nevamiss\\Infrastructure\\Infrastructure_Module' => __DIR__ . '/../..' . '/src/infrastructure/class-infrastructure-module.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\Facebook_Client' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/class-facebook-client.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\Has_Credentials_Trait' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/has-credentials-trait.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\Instagram_Client' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/class-instagram-client.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\Linkedin_Client' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/class-linkedin-client.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\Request_Parameter_Trait' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/request-parameter-trait.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_V1_Strategy' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/x-api-version-strategies/class-x-api-v1-strategy.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_V2_Strategy' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/x-api-version-strategies/class-x-api-v2-strategy.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_Version_Strategy' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/x-api-version-strategies/interface-x-api-version-strategy.php',
        'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Client' => __DIR__ . '/../..' . '/src/infrastructure/networks/clients/class-x-client.php',
        'Nevamiss\\Infrastructure\\Networks\\Contracts\\Network_Clients_Interface' => __DIR__ . '/../..' . '/src/infrastructure/networks/contracts/network-client-interface.php',
        'Nevamiss\\Infrastructure\\Networks\\Media_Network_Collection' => __DIR__ . '/../..' . '/src/infrastructure/networks/class-media-network-collection.php',
        'Nevamiss\\Infrastructure\\Networks\\Network_Authenticator' => __DIR__ . '/../..' . '/src/infrastructure/networks/class-network-authenticator.php',
        'Nevamiss\\Infrastructure\\Networks\\Network_Clients' => __DIR__ . '/../..' . '/src/infrastructure/networks/network-clients.php',
        'Nevamiss\\Infrastructure\\Url_Shortner\\Rebrandly' => __DIR__ . '/../..' . '/src/infrastructure/url-shortner/class-rebrandly.php',
        'Nevamiss\\Infrastructure\\Url_Shortner\\Shortner_Collection' => __DIR__ . '/../..' . '/src/infrastructure/url-shortner/class-shortner-collection.php',
        'Nevamiss\\Infrastructure\\Url_Shortner\\URL_Shortner_Interface' => __DIR__ . '/../..' . '/src/infrastructure/url-shortner/interface-url-shortner.php',
        'Nevamiss\\Infrastructure\\Url_Shortner\\URL_Shortner_Response_Interface' => __DIR__ . '/../..' . '/src/infrastructure/url-shortner/interface-url-shortner-response.php',
        'Nevamiss\\Infrastructure\\Url_Shortner\\Url_Shortner_Response' => __DIR__ . '/../..' . '/src/infrastructure/url-shortner/class-url-shortner-response.php',
        'Nevamiss\\Presentation\\Components\\Component' => __DIR__ . '/../..' . '/src/presentation/components/class-component.php',
        'Nevamiss\\Presentation\\Components\\Component_Runner' => __DIR__ . '/../..' . '/src/presentation/components/class-component-runner.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\Checkbox_Group' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-checkbox-group.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\Input' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-input.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\Label_Hidden_Input' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-hidden-label.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\Select_Field' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-select-field.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\Select_Group_Field' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-select-group.php',
        'Nevamiss\\Presentation\\Components\\Input_Fields\\TextArea' => __DIR__ . '/../..' . '/src/presentation/components/input-fields/class-textarea.php',
        'Nevamiss\\Presentation\\Components\\Renderable' => __DIR__ . '/../..' . '/src/presentation/components/interface-renderable.php',
        'Nevamiss\\Presentation\\Components\\Tabs\\Section' => __DIR__ . '/../..' . '/src/presentation/components/tabs/class-section.php',
        'Nevamiss\\Presentation\\Components\\Tabs\\Tab' => __DIR__ . '/../..' . '/src/presentation/components/tabs/class-tab.php',
        'Nevamiss\\Presentation\\Components\\Wrapper' => __DIR__ . '/../..' . '/src/presentation/components/class-wrapper.php',
        'Nevamiss\\Presentation\\Contracts\\Renderable' => __DIR__ . '/../..' . '/src/presentation/contracts/Renderable.php',
        'Nevamiss\\Presentation\\Pages\\Auto_Share_Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-auto-share-page.php',
        'Nevamiss\\Presentation\\Pages\\Notices_Trait' => __DIR__ . '/../..' . '/src/presentation/pages/notice-trait.php',
        'Nevamiss\\Presentation\\Pages\\Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-page.php',
        'Nevamiss\\Presentation\\Pages\\Schedule_Form' => __DIR__ . '/../..' . '/src/presentation/pages/class-schedule-form.php',
        'Nevamiss\\Presentation\\Pages\\Schedule_View_Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-schedule-view-page.php',
        'Nevamiss\\Presentation\\Pages\\Schedules_Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-schedules-page.php',
        'Nevamiss\\Presentation\\Pages\\Settings_Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-settings-page.php',
        'Nevamiss\\Presentation\\Pages\\Suggestions_Page' => __DIR__ . '/../..' . '/src/presentation/pages/class-suggestions-page.php',
        'Nevamiss\\Presentation\\Pages\\Tables\\Logs_Table_List' => __DIR__ . '/../..' . '/src/presentation/pages/tables/class-logs-table-list.php',
        'Nevamiss\\Presentation\\Pages\\Tables\\Network_Accounts_Table_List' => __DIR__ . '/../..' . '/src/presentation/pages/tables/class-network-accounts-table-list.php',
        'Nevamiss\\Presentation\\Pages\\Tables\\Schedules_Table_List' => __DIR__ . '/../..' . '/src/presentation/pages/tables/class-schedules-table-list.php',
        'Nevamiss\\Presentation\\Pages\\Tables\\Stats_Table_List' => __DIR__ . '/../..' . '/src/presentation/pages/tables/class-stats-table-list.php',
        'Nevamiss\\Presentation\\Pages\\Tables\\Table_List_Trait' => __DIR__ . '/../..' . '/src/presentation/pages/tables/table-list-trait.php',
        'Nevamiss\\Presentation\\Post_Meta\\Post_Meta' => __DIR__ . '/../..' . '/src/presentation/post-meta/class-post-meta.php',
        'Nevamiss\\Presentation\\Presentation_Module' => __DIR__ . '/../..' . '/src/presentation/class-presentation-module.php',
        'Nevamiss\\Presentation\\Tabs\\Bulk_Delete_Interface' => __DIR__ . '/../..' . '/src/presentation/tabs/bulk-delete-interface.php',
        'Nevamiss\\Presentation\\Tabs\\Bulk_Delete_Trait' => __DIR__ . '/../..' . '/src/presentation/tabs/bulk-delete-trait.php',
        'Nevamiss\\Presentation\\Tabs\\General_Tab' => __DIR__ . '/../..' . '/src/presentation/tabs/class-general-tab.php',
        'Nevamiss\\Presentation\\Tabs\\Logs_Tab' => __DIR__ . '/../..' . '/src/presentation/tabs/class-logs-tab.php',
        'Nevamiss\\Presentation\\Tabs\\Network_Accounts_Tab' => __DIR__ . '/../..' . '/src/presentation/tabs/class-network-accounts-tab.php',
        'Nevamiss\\Presentation\\Tabs\\Render_Interface' => __DIR__ . '/../..' . '/src/presentation/tabs/render-interface.php',
        'Nevamiss\\Presentation\\Tabs\\Section_Interface' => __DIR__ . '/../..' . '/src/presentation/tabs/section-interface.php',
        'Nevamiss\\Presentation\\Tabs\\Stats_Tab' => __DIR__ . '/../..' . '/src/presentation/tabs/class-stats-tab.php',
        'Nevamiss\\Presentation\\Tabs\\Tab_Collection' => __DIR__ . '/../..' . '/src/presentation/tabs/class-tab-collection.php',
        'Nevamiss\\Presentation\\Tabs\\Tab_Collection_Interface' => __DIR__ . '/../..' . '/src/presentation/tabs/tab-collection-interface.php',
        'Nevamiss\\Presentation\\Tabs\\Tab_Interface' => __DIR__ . '/../..' . '/src/presentation/tabs/tab-interface.php',
        'Nevamiss\\Presentation\\Tabs\\Upgrade_Tab' => __DIR__ . '/../..' . '/src/presentation/tabs/class-upgrade-tab.php',
        'Nevamiss\\Presentation\\Utils' => __DIR__ . '/../..' . '/src/presentation/class-utils.php',
        'Nevamiss\\Service\\Factory_Module' => __DIR__ . '/../..' . '/src/domain/factory/class-factory-module.php',
        'Nevamiss\\Service\\Repositories_Module' => __DIR__ . '/../..' . '/src/domain/repositories/class-repositories-module.php',
        'Nevamiss\\Service\\Schedule_Collection' => __DIR__ . '/../..' . '/src/services/class-schedule-collection.php',
        'Nevamiss\\Services\\Accounts_Manager' => __DIR__ . '/../..' . '/src/services/class-accounts-manager.php',
        'Nevamiss\\Services\\Ajax' => __DIR__ . '/../..' . '/src/services/class-ajax.php',
        'Nevamiss\\Services\\Contracts\\Cron_Interface' => __DIR__ . '/../..' . '/src/services/contracts/cron-interface.php',
        'Nevamiss\\Services\\Contracts\\Date_Interface' => __DIR__ . '/../..' . '/src/services/contracts/date-interface.php',
        'Nevamiss\\Services\\Contracts\\Logger_Interface' => __DIR__ . '/../..' . '/src/services/contracts/logger-interface.php',
        'Nevamiss\\Services\\Contracts\\Remote_Post_Interface' => __DIR__ . '/../..' . '/src/services/contracts/remote-post-interface.php',
        'Nevamiss\\Services\\Contracts\\Task_Runner_Interface' => __DIR__ . '/../..' . '/src/services/contracts/task-runner-interface.php',
        'Nevamiss\\Services\\Date' => __DIR__ . '/../..' . '/src/services/class-date.php',
        'Nevamiss\\Services\\Form_Validator' => __DIR__ . '/../..' . '/src/services/class-form-validator.php',
        'Nevamiss\\Services\\Http_Request' => __DIR__ . '/../..' . '/src/services/class-http-request.php',
        'Nevamiss\\Services\\Logger' => __DIR__ . '/../..' . '/src/services/class-logger.php',
        'Nevamiss\\Services\\Network_Post_Aggregator' => __DIR__ . '/../..' . '/src/services/class-network-post-aggregator.php',
        'Nevamiss\\Services\\Network_Post_Manager' => __DIR__ . '/../..' . '/src/services/class-network-post-manager.php',
        'Nevamiss\\Services\\Network_Post_Provider' => __DIR__ . '/../..' . '/src/services/class-network-post-provider.php',
        'Nevamiss\\Services\\Row_Action_Handlers\\Accounts_Row_Action_Handler' => __DIR__ . '/../..' . '/src/services/row-action-handlers/class-accounts-row-action-handler.php',
        'Nevamiss\\Services\\Row_Action_Handlers\\Row_Action_Trail' => __DIR__ . '/../..' . '/src/services/row-action-handlers/trait-row-action.php',
        'Nevamiss\\Services\\Row_Action_Handlers\\Schedule_Row_Action_Handler' => __DIR__ . '/../..' . '/src/services/row-action-handlers/class-schedule-row-action-handler.php',
        'Nevamiss\\Services\\Row_Action_Handlers\\Stats_Row_Action_Handler' => __DIR__ . '/../..' . '/src/services/row-action-handlers/class-stat-row-action-handler.php',
        'Nevamiss\\Services\\Schedule_Post_Manager' => __DIR__ . '/../..' . '/src/services/class-schedule-post-manager.php',
        'Nevamiss\\Services\\Schedule_Queue' => __DIR__ . '/../..' . '/src/services/class-schedule-queue.php',
        'Nevamiss\\Services\\Schedule_Tasks_Runner' => __DIR__ . '/../..' . '/src/services/class-schedule-tasks-runner.php',
        'Nevamiss\\Services\\Services_Module' => __DIR__ . '/../..' . '/src/services/class-services-module.php',
        'Nevamiss\\Services\\Settings' => __DIR__ . '/../..' . '/src/services/class-settings.php',
        'Nevamiss\\Services\\Stats_Manager' => __DIR__ . '/../..' . '/src/services/class-stats-manager.php',
        'Nevamiss\\Services\\Task_Runner' => __DIR__ . '/../..' . '/src/services/class-task-runner.php',
        'Nevamiss\\Services\\Url_Shortner_Manager' => __DIR__ . '/../..' . '/src/services/class-url-shortner-manager.php',
        'Nevamiss\\Services\\WP_Cron_Service' => __DIR__ . '/../..' . '/src/services/class-wp-cron-service.php',
        'Psr\\Container\\ContainerExceptionInterface' => __DIR__ . '/..' . '/psr/container/src/ContainerExceptionInterface.php',
        'Psr\\Container\\ContainerInterface' => __DIR__ . '/..' . '/psr/container/src/ContainerInterface.php',
        'Psr\\Container\\NotFoundExceptionInterface' => __DIR__ . '/..' . '/psr/container/src/NotFoundExceptionInterface.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit99513a915d290fd93000398b104730c7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit99513a915d290fd93000398b104730c7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit99513a915d290fd93000398b104730c7::$classMap;

        }, null, ClassLoader::class);
    }
}
