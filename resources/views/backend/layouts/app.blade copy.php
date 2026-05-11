<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->currentLocale()) }}" dir="{{ language_direction() }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
        <link type="image/png" href="{{ asset("img/favicon.png") }}" rel="icon" />
        <link href="{{ asset("img/favicon.png") }}" rel="apple-touch-icon" sizes="76x76" />
        <meta name="keyword" content="{{ setting("meta_keyword") }}" />
        <meta name="description" content="{{ setting("meta_description") }}" />
        <link href="{{ asset("img/favicon.png") }}" rel="shortcut icon" />
        <link type="image/ico" href="{{ asset("img/favicon.png") }}" rel="icon" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield("title") | {{ config("app.name") }}</title>

        {{-- 1. jQuery first --}}
        <script src="{{ asset("vendor/jquery/jquery-3.6.4.min.js") }}"></script>

        {{-- 2. Pin jQuery to window immediately --}}
        <script>
            window.jQuery = window.$ = jQuery;
        </script>

        {{-- 3. Summernote CSS only --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet"/>

        {{-- 4. Vite assets --}}
        @vite(["resources/sass/app-backend.scss", "resources/js/app-backend.js"])

        <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans+Bengali+UI&display=swap" rel="stylesheet" />
        <style>
            body { font-family: Ubuntu, 'Noto Sans Bengali UI', Arial, Helvetica, sans-serif; }
        </style>

        @stack("after-styles")
        <x-google-analytics />
        @livewireStyles
    </head>

    <body>
        <x-selected-theme />

        @include("backend.includes.sidebar")

        <div class="wrapper d-flex flex-column min-vh-100">
            @include("backend.includes.header")

            <div class="body flex grow">
                <div class="container-lg px-4 py-2">
                    @include("flash::message")
                    @include("backend.includes.errors")
                    @yield("content")
                </div>
            </div>

            <x-backend.includes.footer />
        </div>

        {{-- 5. Livewire scripts --}}
        @livewireScripts

        {{-- 6. Summernote JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

        {{-- 7. Filemanager --}}
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

        {{-- 8. Page specific scripts --}}
        @stack("after-scripts")

        {{-- 9. Summernote init --}}
        <script>
            (function($) {

                var LFMButton = function(context) {
                    var ui = $.summernote.ui;
                    return ui.button({
                        contents: '<i class="note-icon-picture"></i>',
                        tooltip: 'Insert image with filemanager',
                        click: function() {
                            window.open(
                                '/laravel-filemanager?type=image',
                                'FileManager',
                                'width=900,height=600'
                            );
                            window.SetUrl = function(lfmItems) {
                                lfmItems.forEach(function(item) {
                                    context.invoke('insertImage', item.url);
                                });
                            };
                        }
                    }).render();
                };

                function initSummernote() {
                    var $el = $('#content');

                    if (!$el.length) return;

                    if ($el.next('.note-editor').length) {
                        $el.summernote('destroy');
                    }

                    $el.summernote({
                        height: 120,
                        toolbar: [
                            ['style',  ['style']],
                            ['font',   ['fontname', 'fontsize', 'bold', 'underline', 'clear']],
                            ['color',  ['color']],
                            ['para',   ['ul', 'ol', 'paragraph']],
                            ['table',  ['table']],
                            ['insert', ['link', 'lfm', 'video']],
                            ['view',   ['codeview', 'undo', 'redo', 'help']],
                        ],
                        buttons: { lfm: LFMButton },
                    });

                    if ($('#button-image').length) {
                        $('#button-image').filemanager('image');
                    }
                }

                $(document).ready(function() {
                    initSummernote();
                });

                document.addEventListener('livewire:init', function() {
                    Livewire.hook('morph.updated', function({ el, component }) {
                        setTimeout(initSummernote, 100);
                    });
                });

            })(window.jQuery);
        </script>
        <script>
document.addEventListener('livewire:initialized', () => {
    initEditor();

    // Re-init when Livewire morphs the DOM (e.g. type changes)
    Livewire.hook('morph.updated', ({ el }) => {
        if (document.getElementById('content')) {
            initEditor();
        }
    });

    // Clear editor when resetForm() fires
    Livewire.on('reset-editor', () => {
        const editor = document.getElementById('content');
        if (editor) {
            editor.value = '';
            syncToLivewire('');
        }
    });
});

function initEditor() {
    const editor = document.getElementById('content');
    const hidden = document.getElementById('content-hidden');

    if (!editor || !hidden) return;

    // Populate editor from existing Livewire value (edit mode)
    if (hidden.value && !editor.value) {
        editor.value = hidden.value;
    }

    // Avoid attaching duplicate listeners
    if (editor._syncAttached) return;
    editor._syncAttached = true;

    // Push changes into Livewire on every keystroke
    editor.addEventListener('input', () => {
        syncToLivewire(editor.value);
    });
}

function syncToLivewire(value) {
    const hidden = document.getElementById('content-hidden');
    if (!hidden) return;

    // Trigger a native input event so Livewire picks up the change
    const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
        window.HTMLInputElement.prototype, 'value'
    ).set;
    nativeInputValueSetter.call(hidden, value);
    hidden.dispatchEvent(new Event('input', { bubbles: true }));
}
</script>

    </body>
</html>