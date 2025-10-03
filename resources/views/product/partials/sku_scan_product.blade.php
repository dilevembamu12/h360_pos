<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                onclick="$('#html5-qrcode-button-torch-off').click();$('#html5-qrcode-button-camera-stop').click()"><span
                    aria-hidden="true">&times;</span></button>
            <button type="" class="btn btn-primary" onclick="lastMessage='0'">***Doubler</button>
        </div>
        <div class="modal-body">

            <link rel="preload" href="{{ asset('custom/skuscan/assets/main.css?v=' . $asset_v) }}">
            <link rel="stylesheet" href="{{ asset('custom/skuscan/assets/main.css?v=' . $asset_v) }}">
            <link rel="preload" href="{{ asset('custom/skuscan/assets/custom.css?v=' . $asset_v) }}">
            <link rel="stylesheet" href="{{ asset('custom/skuscan/assets/custom.css?v=' . $asset_v) }}">
            <link rel="stylesheet" href="{{ asset('custom/skuscan/assets/sharebar.css?v=' . $asset_v) }}">






            <main class="default-content" aria-label="Content">


                <div class="wrapper-content">
                    <link rel="canonical" href="html5-qrcode.html" />
                    <style>
                        #reader {
                            width: 640px;
                        }

                        @media(max-width: 600px) {
                            #reader {
                                width: 300px;
                            }
                        }

                        .empty {
                            display: block;
                            width: 100%;
                            height: 20px;
                        }

                        .alert {
                            padding: 15px;
                            margin-bottom: 20px;
                            border: 1px solid transparent;
                            border-radius: 4px;
                        }

                        .alert-info {
                            color: #31708f;
                            background-color: #d9edf7;
                            border-color: #bce8f1;
                        }

                        .alert-success {
                            color: #3c763d;
                            background-color: #dff0d8;
                            border-color: #d6e9c6;
                        }

                        #scanapp_ad {
                            display: none;
                        }

                        @media(max-width: 1500px) {
                            #scanapp_ad {
                                display: block;
                            }
                        }
                    </style>
                    <link rel="stylesheet"
                        href="{{ asset('custom/skuscan/highlight.js/10.0.3/styles/default.min.css?v=' . $asset_v) }}">


                    <div class="row">
                        <div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
                            <div id="reader" style="display: inline-block;"></div>
                            <div class="empty"></div>
                            <div id="scanned-result"></div>
                        </div>
                    </div>


                    <script src="{{ asset('custom/skuscan/highlight.js/10.0.3/highlight.min.js?v=' . $asset_v) }}"></script>
                    <script src="{{ asset('custom/skuscan/assets/research/html5qrcode/html5-qrcode.min.v2.3.0.js?v=' . $asset_v) }}"></script>

                    <script>
                        lastMessage = "0";
                        //gestion des sons
                        function triggerSound(sound) {
                            if (sound='success') {
                                var audio = $('#success-audio')[0];
                                if (audio !== undefined) {
                                    audio.play();
                                }
                            } else if (sound='error') {
                                var audio = $('#error-audio')[0];
                                console.log(audio);
                                if (audio !== undefined) {
                                    audio.play();
                                }
                            } else if (sound='warning') {
                                var audio = $('#warning-audio')[0];
                                if (audio !== undefined) {
                                    audio.play();
                                }
                            }
                        }

                        function docReady(fn) {
                            // see if DOM is already available
                            if (document.readyState === "complete" || document.readyState === "interactive") {
                                // call on next available tick
                                setTimeout(fn, 1);
                            } else {
                                document.addEventListener("DOMContentLoaded", fn);
                            }
                        }
                        /** Ugly function to write the results to a table dynamically. */
                        function printScanResultPretty(codeId, decodedText, decodedResult) {
                            //pour la recherche produit dans l'ecran pos
                            $('#search_product').val(decodedText);
                            $('input#search_product').autocomplete("search");
                            /****************************************************/
                            //pour mettre le barcode de sku lors de la creation du produit
                            $('#sku').val(decodedText);
                            /*************************************************************/

                            //alert(decodedText);
                            let resultSection = document.getElementById('scanned-result');
                            let tableBodyId = "scanned-result-table-body";
                            if (!document.getElementById(tableBodyId)) {
                                let table = document.createElement("table");
                                table.className = "table table-bordered table-striped dataTable no-footer";
                                table.style.width = "100%";
                                resultSection.appendChild(table);
                                let theader = document.createElement('thead');
                                let trow = document.createElement('tr');
                                let th1 = document.createElement('td');
                                th1.innerText = "Count";
                                let th2 = document.createElement('td');
                                th2.innerText = "Format";
                                let th3 = document.createElement('td');
                                th3.innerText = "Result";
                                trow.appendChild(th1);
                                trow.appendChild(th2);
                                trow.appendChild(th3);
                                theader.appendChild(trow);
                                table.appendChild(theader);
                                let tbody = document.createElement("tbody");
                                tbody.id = tableBodyId;
                                table.appendChild(tbody);
                            }
                            let tbody = document.getElementById(tableBodyId);
                            let trow = document.createElement('tr');
                            let td1 = document.createElement('td');
                            td1.innerText = `${codeId}`;
                            let td2 = document.createElement('td');
                            td2.innerText = `${decodedResult.result.format.formatName}`;
                            let td3 = document.createElement('td');
                            td3.innerText = `${decodedText}`;
                            trow.appendChild(td1);
                            trow.appendChild(td2);
                            trow.appendChild(td3);
                            tbody.appendChild(trow);
                        }
                        docReady(function() {
                            hljs.initHighlightingOnLoad();

                            var codeId = 0;

                            function onScanSuccess(decodedText, decodedResult) {
                                /**
                                 * If you following the code example of this page by looking at the
                                 * source code of the demo page - good job!!
                                 * 
                                 * Tip: update this function with a success callback of your choise.
                                 */

                                if (lastMessage !== decodedText) {
                                    lastMessage = decodedText;
                                    printScanResultPretty(codeId, decodedText, decodedResult);
                                    triggerSound('success');
                                    ++codeId;

                                }else{
                                  //alert(1111);
                                  //triggerSound('error');
                                }
                                /*
                                lastMessage = decodedText;
                                  printScanResultPretty(codeId, decodedText, decodedResult);
                                  ++codeId;
                                */

                            }
                            var qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
                                // Square QR Box, with size = 80% of the min edge.
                                var minEdgeSizeThreshold = 250;
                                var edgeSizePercentage = 0.75;
                                var minEdgeSize = (viewfinderWidth > viewfinderHeight) ?
                                    viewfinderHeight : viewfinderWidth;
                                var qrboxEdgeSize = Math.floor(minEdgeSize * edgeSizePercentage);
                                if (qrboxEdgeSize < minEdgeSizeThreshold) {
                                    if (minEdgeSize < minEdgeSizeThreshold) {
                                        return {
                                            width: minEdgeSize,
                                            height: minEdgeSize
                                        };
                                    } else {
                                        return {
                                            width: minEdgeSizeThreshold,
                                            height: minEdgeSizeThreshold
                                        };
                                    }
                                }
                                return {
                                    width: qrboxEdgeSize,
                                    height: qrboxEdgeSize
                                };
                            }
                            let html5QrcodeScanner = new Html5QrcodeScanner(
                                "reader", {
                                    fps: 10,
                                    qrbox: qrboxFunction,
                                    // Important notice: this is experimental feature, use it at your
                                    // own risk. See documentation in
                                    // mebjas@/html5-qrcode/src/experimental-features.ts
                                    experimentalFeatures: {
                                        useBarCodeDetectorIfSupported: true
                                    },
                                    rememberLastUsedCamera: true,
                                    showTorchButtonIfSupported: true
                                });
                            html5QrcodeScanner.render(onScanSuccess);
                        });
                    </script>
                </div>
            </main>









        </div>
        <div class="modal-footer">
            <button type="" class="btn btn-primary" onclick="lastMessage='0'">***Doubler</button>
            <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="$('#html5-qrcode-button-torch-off').click();$('#html5-qrcode-button-camera-stop').click()">@lang('messages.close')</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
