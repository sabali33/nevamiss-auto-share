<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'Inpsyde\\Modularity\\Container\\ContainerConfigurator' => $vendorDir . '/inpsyde/modularity/src/Container/ContainerConfigurator.php',
    'Inpsyde\\Modularity\\Container\\PackageProxyContainer' => $vendorDir . '/inpsyde/modularity/src/Container/PackageProxyContainer.php',
    'Inpsyde\\Modularity\\Container\\ReadOnlyContainer' => $vendorDir . '/inpsyde/modularity/src/Container/ReadOnlyContainer.php',
    'Inpsyde\\Modularity\\Container\\ServiceExtensions' => $vendorDir . '/inpsyde/modularity/src/Container/ServiceExtensions.php',
    'Inpsyde\\Modularity\\Module\\ExecutableModule' => $vendorDir . '/inpsyde/modularity/src/Module/ExecutableModule.php',
    'Inpsyde\\Modularity\\Module\\ExtendingModule' => $vendorDir . '/inpsyde/modularity/src/Module/ExtendingModule.php',
    'Inpsyde\\Modularity\\Module\\FactoryModule' => $vendorDir . '/inpsyde/modularity/src/Module/FactoryModule.php',
    'Inpsyde\\Modularity\\Module\\Module' => $vendorDir . '/inpsyde/modularity/src/Module/Module.php',
    'Inpsyde\\Modularity\\Module\\ModuleClassNameIdTrait' => $vendorDir . '/inpsyde/modularity/src/Module/ModuleClassNameIdTrait.php',
    'Inpsyde\\Modularity\\Module\\ServiceModule' => $vendorDir . '/inpsyde/modularity/src/Module/ServiceModule.php',
    'Inpsyde\\Modularity\\Package' => $vendorDir . '/inpsyde/modularity/src/Package.php',
    'Inpsyde\\Modularity\\Properties\\BaseProperties' => $vendorDir . '/inpsyde/modularity/src/Properties/BaseProperties.php',
    'Inpsyde\\Modularity\\Properties\\LibraryProperties' => $vendorDir . '/inpsyde/modularity/src/Properties/LibraryProperties.php',
    'Inpsyde\\Modularity\\Properties\\PluginProperties' => $vendorDir . '/inpsyde/modularity/src/Properties/PluginProperties.php',
    'Inpsyde\\Modularity\\Properties\\Properties' => $vendorDir . '/inpsyde/modularity/src/Properties/Properties.php',
    'Inpsyde\\Modularity\\Properties\\ThemeProperties' => $vendorDir . '/inpsyde/modularity/src/Properties/ThemeProperties.php',
    'Nevamiss\\Application\\Application_Module' => $baseDir . '/src/application/class-application-module.php',
    'Nevamiss\\Application\\Assets' => $baseDir . '/src/application/class-assets.php',
    'Nevamiss\\Application\\Compatibility\\Version_Dependency_Provider' => $baseDir . '/src/application/compatibility/class-version-dependency-provider.php',
    'Nevamiss\\Application\\Compatibility\\Versions_Dependency_Interface' => $baseDir . '/src/application/compatibility/versions-dependency-interface.php',
    'Nevamiss\\Application\\DB' => $baseDir . '/src/application/class-db.php',
    'Nevamiss\\Application\\Not_Found_Exception' => $baseDir . '/src/application/class-not-found-exception.php',
    'Nevamiss\\Application\\Post_Query\\Query' => $baseDir . '/src/application/post-query/class-query.php',
    'Nevamiss\\Application\\Setup' => $baseDir . '/src/application/class-setup.php',
    'Nevamiss\\Application\\Task' => $baseDir . '/src/application/class-task.php',
    'Nevamiss\\Application\\Uninstall' => $baseDir . '/src/application/class-uninstall.php',
    'Nevamiss\\Domain\\Contracts\\Create_Interface' => $baseDir . '/src/domain/contracts/create-interface.php',
    'Nevamiss\\Domain\\Contracts\\Delete_All_Interface' => $baseDir . '/src/domain/contracts/delete-all-interface.php',
    'Nevamiss\\Domain\\Contracts\\Delete_Interface' => $baseDir . '/src/domain/contracts/delete-interface.php',
    'Nevamiss\\Domain\\Contracts\\Get_All_Interface' => $baseDir . '/src/domain/contracts/get-all-interface.php',
    'Nevamiss\\Domain\\Contracts\\Get_One_Interface' => $baseDir . '/src/domain/contracts/get-one-interface.php',
    'Nevamiss\\Domain\\Contracts\\Update_Interface' => $baseDir . '/src/domain/contracts/update-interface.php',
    'Nevamiss\\Domain\\DTO\\Share_Response' => $baseDir . '/src/domain/dto/class-share-response.php',
    'Nevamiss\\Domain\\Entities\\Log' => $baseDir . '/src/domain/entities/class-log.php',
    'Nevamiss\\Domain\\Entities\\Network_Account' => $baseDir . '/src/domain/entities/class-network-account.php',
    'Nevamiss\\Domain\\Entities\\Schedule' => $baseDir . '/src/domain/entities/class-schedule.php',
    'Nevamiss\\Domain\\Entities\\Schedule_Queue' => $baseDir . '/src/domain/entities/class-schedule-queue.php',
    'Nevamiss\\Domain\\Entities\\Stats' => $baseDir . '/src/domain/entities/class-stat.php',
    'Nevamiss\\Domain\\Entities\\Task' => $baseDir . '/src/domain/entities/class-task.php',
    'Nevamiss\\Domain\\Factory\\Factory' => $baseDir . '/src/domain/factory/class-factory.php',
    'Nevamiss\\Domain\\Repositories\\Command_Query' => $baseDir . '/src/domain/repositories/class-command-query.php',
    'Nevamiss\\Domain\\Repositories\\Count_Model_Trait' => $baseDir . '/src/domain/repositories/trait-count-model.php',
    'Nevamiss\\Domain\\Repositories\\Create_Trait' => $baseDir . '/src/domain/repositories/trait-create.php',
    'Nevamiss\\Domain\\Repositories\\Delete_All_Trait' => $baseDir . '/src/domain/repositories/trait-delete-all.php',
    'Nevamiss\\Domain\\Repositories\\Delete_Trait' => $baseDir . '/src/domain/repositories/trait-delete.php',
    'Nevamiss\\Domain\\Repositories\\Get_All_Trait' => $baseDir . '/src/domain/repositories/trait-get-all.php',
    'Nevamiss\\Domain\\Repositories\\Get_One_Trait' => $baseDir . '/src/domain/repositories/trait-get-one.php',
    'Nevamiss\\Domain\\Repositories\\Logger_Repository' => $baseDir . '/src/domain/repositories/class-logger-repository.php',
    'Nevamiss\\Domain\\Repositories\\Network_Account_Repository' => $baseDir . '/src/domain/repositories/class-network-account-repository.php',
    'Nevamiss\\Domain\\Repositories\\Posts_Stats_Repository' => $baseDir . '/src/domain/repositories/class-posts-stats-repository.php',
    'Nevamiss\\Domain\\Repositories\\Repository_Common_Trait' => $baseDir . '/src/domain/repositories/trait-repository-common.php',
    'Nevamiss\\Domain\\Repositories\\Schedule_Queue_Repository' => $baseDir . '/src/domain/repositories/class-schedule-queue-repository.php',
    'Nevamiss\\Domain\\Repositories\\Schedule_Repository' => $baseDir . '/src/domain/repositories/class-schedule-repository.php',
    'Nevamiss\\Domain\\Repositories\\Task_Repository' => $baseDir . '/src/domain/repositories/class-task-repository.php',
    'Nevamiss\\Domain\\Repositories\\To_Model_Trait' => $baseDir . '/src/domain/repositories/trait-to-model.php',
    'Nevamiss\\Domain\\Repositories\\Update_Trait' => $baseDir . '/src/domain/repositories/trait-update.php',
    'Nevamiss\\Infrastructure\\Infrastructure_Module' => $baseDir . '/src/infrastructure/class-infrastructure-module.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\Facebook_Client' => $baseDir . '/src/infrastructure/networks/clients/class-facebook-client.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\Has_Credentials_Trait' => $baseDir . '/src/infrastructure/networks/clients/has-credentials-trait.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\Instagram_Client' => $baseDir . '/src/infrastructure/networks/clients/class-instagram-client.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\Linkedin_Client' => $baseDir . '/src/infrastructure/networks/clients/class-linkedin-client.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\Request_Parameter_Trait' => $baseDir . '/src/infrastructure/networks/clients/request-parameter-trait.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_V1_Strategy' => $baseDir . '/src/infrastructure/networks/clients/x-api-version-strategies/class-x-api-v1-strategy.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_V2_Strategy' => $baseDir . '/src/infrastructure/networks/clients/x-api-version-strategies/class-x-api-v2-strategy.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Api_Version_Strategy\\X_Api_Version_Strategy' => $baseDir . '/src/infrastructure/networks/clients/x-api-version-strategies/interface-x-api-version-strategy.php',
    'Nevamiss\\Infrastructure\\Networks\\Clients\\X_Client' => $baseDir . '/src/infrastructure/networks/clients/class-x-client.php',
    'Nevamiss\\Infrastructure\\Networks\\Contracts\\Network_Clients_Interface' => $baseDir . '/src/infrastructure/networks/contracts/network-client-interface.php',
    'Nevamiss\\Infrastructure\\Networks\\Media_Network_Collection' => $baseDir . '/src/infrastructure/networks/class-media-network-collection.php',
    'Nevamiss\\Infrastructure\\Networks\\Network_Authenticator' => $baseDir . '/src/infrastructure/networks/class-network-authenticator.php',
    'Nevamiss\\Infrastructure\\Networks\\Network_Clients' => $baseDir . '/src/infrastructure/networks/network-clients.php',
    'Nevamiss\\Infrastructure\\Url_Shortner\\Rebrandly' => $baseDir . '/src/infrastructure/url-shortner/class-rebrandly.php',
    'Nevamiss\\Infrastructure\\Url_Shortner\\Shortner_Collection' => $baseDir . '/src/infrastructure/url-shortner/class-shortner-collection.php',
    'Nevamiss\\Infrastructure\\Url_Shortner\\URL_Shortner_Interface' => $baseDir . '/src/infrastructure/url-shortner/interface-url-shortner.php',
    'Nevamiss\\Infrastructure\\Url_Shortner\\URL_Shortner_Response_Interface' => $baseDir . '/src/infrastructure/url-shortner/interface-url-shortner-response.php',
    'Nevamiss\\Infrastructure\\Url_Shortner\\Url_Shortner_Response' => $baseDir . '/src/infrastructure/url-shortner/class-url-shortner-response.php',
    'Nevamiss\\Presentation\\Components\\Component' => $baseDir . '/src/presentation/components/class-component.php',
    'Nevamiss\\Presentation\\Components\\Component_Runner' => $baseDir . '/src/presentation/components/class-component-runner.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\Checkbox_Group' => $baseDir . '/src/presentation/components/input-fields/class-checkbox-group.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\Input' => $baseDir . '/src/presentation/components/input-fields/class-input.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\Label_Hidden_Input' => $baseDir . '/src/presentation/components/input-fields/class-hidden-label.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\Select_Field' => $baseDir . '/src/presentation/components/input-fields/class-select-field.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\Select_Group_Field' => $baseDir . '/src/presentation/components/input-fields/class-select-group.php',
    'Nevamiss\\Presentation\\Components\\Input_Fields\\TextArea' => $baseDir . '/src/presentation/components/input-fields/class-textarea.php',
    'Nevamiss\\Presentation\\Components\\Renderable' => $baseDir . '/src/presentation/components/interface-renderable.php',
    'Nevamiss\\Presentation\\Components\\Tabs\\Section' => $baseDir . '/src/presentation/components/tabs/class-section.php',
    'Nevamiss\\Presentation\\Components\\Tabs\\Tab' => $baseDir . '/src/presentation/components/tabs/class-tab.php',
    'Nevamiss\\Presentation\\Components\\Wrapper' => $baseDir . '/src/presentation/components/class-wrapper.php',
    'Nevamiss\\Presentation\\Contracts\\Renderable' => $baseDir . '/src/presentation/contracts/Renderable.php',
    'Nevamiss\\Presentation\\Pages\\Auto_Share_Page' => $baseDir . '/src/presentation/pages/class-auto-share-page.php',
    'Nevamiss\\Presentation\\Pages\\Notices_Trait' => $baseDir . '/src/presentation/pages/notice-trait.php',
    'Nevamiss\\Presentation\\Pages\\Page' => $baseDir . '/src/presentation/pages/class-page.php',
    'Nevamiss\\Presentation\\Pages\\Schedule_Form' => $baseDir . '/src/presentation/pages/class-schedule-form.php',
    'Nevamiss\\Presentation\\Pages\\Schedule_View_Page' => $baseDir . '/src/presentation/pages/class-schedule-view-page.php',
    'Nevamiss\\Presentation\\Pages\\Schedules_Page' => $baseDir . '/src/presentation/pages/class-schedules-page.php',
    'Nevamiss\\Presentation\\Pages\\Settings_Page' => $baseDir . '/src/presentation/pages/class-settings-page.php',
    'Nevamiss\\Presentation\\Pages\\Suggestions_Page' => $baseDir . '/src/presentation/pages/class-suggestions-page.php',
    'Nevamiss\\Presentation\\Pages\\Tables\\Logs_Table_List' => $baseDir . '/src/presentation/pages/tables/class-logs-table-list.php',
    'Nevamiss\\Presentation\\Pages\\Tables\\Network_Accounts_Table_List' => $baseDir . '/src/presentation/pages/tables/class-network-accounts-table-list.php',
    'Nevamiss\\Presentation\\Pages\\Tables\\Schedules_Table_List' => $baseDir . '/src/presentation/pages/tables/class-schedules-table-list.php',
    'Nevamiss\\Presentation\\Pages\\Tables\\Stats_Table_List' => $baseDir . '/src/presentation/pages/tables/class-stats-table-list.php',
    'Nevamiss\\Presentation\\Pages\\Tables\\Table_List_Trait' => $baseDir . '/src/presentation/pages/tables/table-list-trait.php',
    'Nevamiss\\Presentation\\Post_Meta\\Post_Meta' => $baseDir . '/src/presentation/post-meta/class-post-meta.php',
    'Nevamiss\\Presentation\\Presentation_Module' => $baseDir . '/src/presentation/class-presentation-module.php',
    'Nevamiss\\Presentation\\Tabs\\Bulk_Delete_Interface' => $baseDir . '/src/presentation/tabs/bulk-delete-interface.php',
    'Nevamiss\\Presentation\\Tabs\\Bulk_Delete_Trait' => $baseDir . '/src/presentation/tabs/bulk-delete-trait.php',
    'Nevamiss\\Presentation\\Tabs\\General_Tab' => $baseDir . '/src/presentation/tabs/class-general-tab.php',
    'Nevamiss\\Presentation\\Tabs\\Logs_Tab' => $baseDir . '/src/presentation/tabs/class-logs-tab.php',
    'Nevamiss\\Presentation\\Tabs\\Network_Accounts_Tab' => $baseDir . '/src/presentation/tabs/class-network-accounts-tab.php',
    'Nevamiss\\Presentation\\Tabs\\Render_Interface' => $baseDir . '/src/presentation/tabs/render-interface.php',
    'Nevamiss\\Presentation\\Tabs\\Section_Interface' => $baseDir . '/src/presentation/tabs/section-interface.php',
    'Nevamiss\\Presentation\\Tabs\\Stats_Tab' => $baseDir . '/src/presentation/tabs/class-stats-tab.php',
    'Nevamiss\\Presentation\\Tabs\\Tab_Collection' => $baseDir . '/src/presentation/tabs/class-tab-collection.php',
    'Nevamiss\\Presentation\\Tabs\\Tab_Collection_Interface' => $baseDir . '/src/presentation/tabs/tab-collection-interface.php',
    'Nevamiss\\Presentation\\Tabs\\Tab_Interface' => $baseDir . '/src/presentation/tabs/tab-interface.php',
    'Nevamiss\\Presentation\\Tabs\\Upgrade_Tab' => $baseDir . '/src/presentation/tabs/class-upgrade-tab.php',
    'Nevamiss\\Presentation\\Utils' => $baseDir . '/src/presentation/class-utils.php',
    'Nevamiss\\Service\\Factory_Module' => $baseDir . '/src/domain/factory/class-factory-module.php',
    'Nevamiss\\Service\\Repositories_Module' => $baseDir . '/src/domain/repositories/class-repositories-module.php',
    'Nevamiss\\Service\\Schedule_Collection' => $baseDir . '/src/services/class-schedule-collection.php',
    'Nevamiss\\Services\\Accounts_Manager' => $baseDir . '/src/services/class-accounts-manager.php',
    'Nevamiss\\Services\\Ajax' => $baseDir . '/src/services/class-ajax.php',
    'Nevamiss\\Services\\Contracts\\Cron_Interface' => $baseDir . '/src/services/contracts/cron-interface.php',
    'Nevamiss\\Services\\Contracts\\Date_Interface' => $baseDir . '/src/services/contracts/date-interface.php',
    'Nevamiss\\Services\\Contracts\\Logger_Interface' => $baseDir . '/src/services/contracts/logger-interface.php',
    'Nevamiss\\Services\\Contracts\\Remote_Post_Interface' => $baseDir . '/src/services/contracts/remote-post-interface.php',
    'Nevamiss\\Services\\Contracts\\Task_Runner_Interface' => $baseDir . '/src/services/contracts/task-runner-interface.php',
    'Nevamiss\\Services\\Date' => $baseDir . '/src/services/class-date.php',
    'Nevamiss\\Services\\Form_Validator' => $baseDir . '/src/services/class-form-validator.php',
    'Nevamiss\\Services\\Http_Request' => $baseDir . '/src/services/class-http-request.php',
    'Nevamiss\\Services\\Logger' => $baseDir . '/src/services/class-logger.php',
    'Nevamiss\\Services\\Network_Post_Aggregator' => $baseDir . '/src/services/class-network-post-aggregator.php',
    'Nevamiss\\Services\\Network_Post_Manager' => $baseDir . '/src/services/class-network-post-manager.php',
    'Nevamiss\\Services\\Network_Post_Provider' => $baseDir . '/src/services/class-network-post-provider.php',
    'Nevamiss\\Services\\Row_Action_Handlers\\Accounts_Row_Action_Handler' => $baseDir . '/src/services/row-action-handlers/class-accounts-row-action-handler.php',
    'Nevamiss\\Services\\Row_Action_Handlers\\Row_Action_Trail' => $baseDir . '/src/services/row-action-handlers/trait-row-action.php',
    'Nevamiss\\Services\\Row_Action_Handlers\\Schedule_Row_Action_Handler' => $baseDir . '/src/services/row-action-handlers/class-schedule-row-action-handler.php',
    'Nevamiss\\Services\\Row_Action_Handlers\\Stats_Row_Action_Handler' => $baseDir . '/src/services/row-action-handlers/class-stat-row-action-handler.php',
    'Nevamiss\\Services\\Schedule_Post_Manager' => $baseDir . '/src/services/class-schedule-post-manager.php',
    'Nevamiss\\Services\\Schedule_Queue' => $baseDir . '/src/services/class-schedule-queue.php',
    'Nevamiss\\Services\\Schedule_Tasks_Runner' => $baseDir . '/src/services/class-schedule-tasks-runner.php',
    'Nevamiss\\Services\\Services_Module' => $baseDir . '/src/services/class-services-module.php',
    'Nevamiss\\Services\\Settings' => $baseDir . '/src/services/class-settings.php',
    'Nevamiss\\Services\\Stats_Manager' => $baseDir . '/src/services/class-stats-manager.php',
    'Nevamiss\\Services\\Task_Runner' => $baseDir . '/src/services/class-task-runner.php',
    'Nevamiss\\Services\\Url_Shortner_Manager' => $baseDir . '/src/services/class-url-shortner-manager.php',
    'Nevamiss\\Services\\WP_Cron_Service' => $baseDir . '/src/services/class-wp-cron-service.php',
    'Psr\\Container\\ContainerExceptionInterface' => $vendorDir . '/psr/container/src/ContainerExceptionInterface.php',
    'Psr\\Container\\ContainerInterface' => $vendorDir . '/psr/container/src/ContainerInterface.php',
    'Psr\\Container\\NotFoundExceptionInterface' => $vendorDir . '/psr/container/src/NotFoundExceptionInterface.php',
);
