<a href="{{ $action->getRoute($routeParams ?? []) }}" title="{{ $action->getTitle() }}" {!! $action->convertAttributesToHtml() !!}>
    <i class="{{ $action->getIcon() }} mr-2"></i> <span class="hidden-xs hidden-sm">{{ $action->getTitle() }}</span>
</a>
