@php
    $crumbElements = [];
    foreach (($items ?? []) as $i => $crumb) {
        $node = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['name']];
        if (! empty($crumb['url'])) {
            $node['item'] = $crumb['url'];
        }
        $crumbElements[] = $node;
    }
    $crumbJson = json_encode(
        ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbElements],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
@endphp
<script type="application/ld+json">{!! $crumbJson !!}</script>
