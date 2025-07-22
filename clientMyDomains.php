<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\View\Menu\Item;

add_hook('ClientAreaHomepagePanels', 1, function (Item $homePagePanels) {
    $client = Menu::context('client');

    // Get all active domains for the logged-in user
    $clientDomains = Capsule::table('tbldomains')
        ->where('userid', $client->id)
        ->where('status', 'Active')
        ->select('id', 'domain')
        ->orderBy('domain', 'desc')
        ->limit(10)
        ->get();

    $totalDomains = $clientDomains->count();

    if ($totalDomains > 0) {
        $homePagePanels->addChild('Manage Domains', [
            'label' => 'Your Domains (' . $totalDomains . ')',
            'icon' => 'fas fa-globe',
            'order' => 1,
            'extras' => [
                'color' => 'green',
                'btn-link' => 'clientarea.php?action=domains',
                'btn-text' => Lang::trans('clientareanavdomains'),
                'btn-icon' => 'fas fa-arrow-right',
            ],
        ]);

        foreach ($clientDomains as $key => $domain) {
            $homePagePanels->getChild('Manage Domains')
                ->addChild($key)
                ->setLabel('
                    <span style="
                        display: inline-flex;
                        align-items: center;
                        font-family: \'Open Sans\', Arial, sans-serif;
                        font-size: 0.9rem;
                        letter-spacing: 0.03em;
                        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
                    ">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             width="18" height="18" viewBox="0 0 24 24" fill="none" 
                             stroke="#1e90ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                             style="margin-right:8px; flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M2 12h20"/>
                            <path d="M12 2a15 15 0 0 1 0 20"/>
                            <path d="M12 2a15 15 0 0 0 0 20"/>
                        </svg>
                        <span style="font-weight: 600;">Domain:</span>
                        <span style="font-weight: normal; margin-left: 4px;">' . htmlspecialchars($domain->domain) . '</span>
                    </span>
                ')
                ->setUri('clientarea.php?action=domaindetails&id=' . intval($domain->id))
                ->setAttribute('target', '_self');
        }
    }
});
