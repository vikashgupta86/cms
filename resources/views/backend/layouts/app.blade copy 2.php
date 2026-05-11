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

        <!-- Shortcut Icon -->
        <link href="{{ asset("img/favicon.png") }}" rel="shortcut icon" />
        <link type="image/ico" href="{{ asset("img/favicon.png") }}" rel="icon" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield("title") | {{ config("app.name") }}</title>

        <script src="{{ asset("vendor/jquery/jquery-3.6.4.min.js") }}"></script>
        {{-- Summernote CSS --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet"/>

        @vite(["resources/sass/app-backend.scss", "resources/js/app-backend.js"])

        <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans+Bengali+UI&display=swap" rel="stylesheet" />
        <style>
            body {
                font-family: Ubuntu, 'Noto Sans Bengali UI', Arial, Helvetica, sans-serif;
            }
        </style>

        @stack("after-styles")

        <x-google-analytics />

        @livewireStyles
    </head>

    <body>
        <x-selected-theme />

        <!-- Sidebar -->
        @include("backend.includes.sidebar")
        <!-- /Sidebar -->

        <div class="wrapper d-flex flex-column min-vh-100">
            {{-- header --}}
            @include("backend.includes.header")

            <div class="body flex grow">
                <div class="container-lg px-4 py-2">
                    @include("flash::message")

                    <!-- Errors block -->
                    @include("backend.includes.errors")
                    <!-- / Errors block -->

                    <!-- Main content block -->
                    @yield("content")
                    <!-- / Main content block -->
                </div>
            </div>

            {{-- Footer block --}}
            <x-backend.includes.footer />
        </div>

        <!-- Scripts -->
        @livewireScripts

        {{-- Summernote JS --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

        {{-- Filemanager --}}
        <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

        {{-- Page specific scripts --}}
        @stack("after-scripts")

        {{-- Summernote init --}}
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

                /**
                 * Push a value into the Livewire hidden input so Livewire
                 * detects the change and updates its $content property.
                 */
                function syncContentToLivewire(value) {
                    var hidden = document.getElementById('content-hidden');
                    if (!hidden) return;

                    var nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                        window.HTMLInputElement.prototype, 'value'
                    ).set;
                    nativeInputValueSetter.call(hidden, value);
                    hidden.dispatchEvent(new Event('input', { bubbles: true }));
                }

                function initSummernote() {
                    var $el = $('#content');

                    if (!$el.length) return;

                    // Destroy existing instance before re-initialising
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

                        // FIX: sync every keystroke/paste/toolbar action to Livewire
                        callbacks: {
                            onChange: function(contents) {
                                syncContentToLivewire(contents);
                            },
                            onBlur: function() {
                                // Extra safety: also sync on blur
                                syncContentToLivewire($el.summernote('code'));
                            }
                        }
                    });

                    // FIX: populate the editor when editing an existing record.
                    // The hidden input already holds the Livewire value at mount time.
                    var hidden = document.getElementById('content-hidden');
                    if (hidden && hidden.value) {
                        $el.summernote('code', hidden.value);
                    }

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

                // Clear editor when Livewire fires the reset-editor event
                document.addEventListener('livewire:initialized', function() {
                    Livewire.on('reset-editor', function() {
                        var $el = $('#content');
                        if ($el.length && $el.next('.note-editor').length) {
                            $el.summernote('code', '');
                        }
                        syncContentToLivewire('');
                    });
                });

            })(window.jQuery);
        </script>
        <!-- / Scripts -->

    </body>
</html>