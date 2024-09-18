<!DOCTYPE html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            border-collapse: collapse;
        }

        .signal-container {
            display: flex;
            gap: 20px;
        }

        .signal {
            width: 60px;
            height: 180px;
            background-color: #333;
            padding: 10px;
            border-radius: 10px;
        }

        .light {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
            border-radius: 50%;
            background-color: #555;
        }

        .light.red { background-color: red; }
        .light.green { background-color: green; }
        .light.yellow { background-color: yellow; }

        .inactive {
            background-color: #555 !important;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-sm-4">
            <h1>Signal List</h1>
            <button id="createSignalBtn">Create</button>
            <table>
                <thead>
                    <tr>
                        <th>Sequence</th>
                        <th>Green Interval (s)</th>
                        <th>Yellow Interval (s)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="signalTableBody">
                    @foreach ($signals as $signal)
                        <tr id="signal_{{ $signal->id }}">
                            <td>{{ json_encode($signal->sequence) }}</td>
                            <td>{{ $signal->green_internal }}</td>
                            <td>{{ $signal->yellow_internal }}</td>
                            <td>
                                <button class="edit-btn" data-id="{{ $signal->id }}">Edit</button>
                                <button class="delete-btn" data-id="{{ $signal->id }}">Delete</button>
                                <button class="start-btn" data-id="{{ $signal->id }}">Start</button>
                                <button class="stop-btn" data-id="{{ $signal->id }}">Stop</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <div class="signal-container">
                <div id="signalA" class="signal">
                    <div class="light red" id="signalA-red"></div>
                    <div class="light yellow inactive" id="signalA-yellow"></div>
                    <div class="light green inactive" id="signalA-green"></div>
                </div>

                <div id="signalB" class="signal">
                    <div class="light red" id="signalB-red"></div>
                    <div class="light yellow inactive" id="signalB-yellow"></div>
                    <div class="light green inactive" id="signalB-green"></div>
                </div>

                <div id="signalC" class="signal">
                    <div class="light red" id="signalC-red"></div>
                    <div class="light yellow inactive" id="signalC-yellow"></div>
                    <div class="light green inactive" id="signalC-green"></div>
                </div>

                <div id="signalD" class="signal">
                    <div class="light red" id="signalD-red"></div>
                    <div class="light yellow inactive" id="signalD-yellow"></div>
                    <div class="light green inactive" id="signalD-green"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="signalModal" style="display:none;">
        <form id="signalForm">
            <input type="hidden" id="signal_id">
            <label>Sequence:</label>
            <input type="text" id="sequence" required><br>
            <label>Green Interval:</label>
            <input type="number" id="green_internal" required><br>
            <label>Yellow Interval:</label>
            <input type="number" id="yellow_internal" required><br>
            <button type="submit" id="saveSignalBtn">Save</button>
        </form>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#createSignalBtn').on('click', function() {
            $('#signal_id').val('');
            $('#sequence').val('');
            $('#green_interval').val('');
            $('#yellow_internal').val('');
            $('#signalModal').show();
        });

        $('#signalForm').on('submit', function(e) {
            e.preventDefault();
            let signalId = $('#signal_id').val();
            let url = signalId ? '/signals/update/' + signalId : '/signals/store';
            let method = signalId ? 'POST' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: {
                    sequence: $('#sequence').val().split(','),
                    green_internal: $('#green_internal').val(),
                    yellow_internal: $('#yellow_internal').val(),
                },
                success: function(response) {
                    $('#signalModal').hide();
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            let signalId = $(this).data('id');
            $.get('/signals/' + signalId + '/edit', function(signal) {
                $('#signal_id').val(signal.id);
                $('#sequence').val(signal.sequence);
                $('#green_internal').val(signal.green_internal);
                $('#yellow_internal').val(signal.yellow_internal);
                $('#signalModal').show();
            });
        });

        $(document).on('click', '.delete-btn', function() {
            let signalId = $(this).data('id');
            if (confirm('Are you sure you want to delete this signal?')) {
                $.ajax({
                    url: '/signals/delete/' + signalId,
                    type: 'DELETE',
                    success: function(response) {
                        $('#signal_' + signalId).remove();
                    }
                });
            }
        });

        let interval;
        let currentSignalIndex = 0;
        let signals = ['A', 'B', 'C', 'D'];
        let greenInternal, yellowInternal;

        $('.start-btn').on('click', function () {
            let signalId = $(this).data('id');
            $.get('/signals/' + signalId + '/edit', function(signal) {
                greenInternal = parseInt(signal.green_internal) * 1000;
                yellowInternal = parseInt(signal.yellow_internal) * 1000;
                signals = signal.sequence.toString().split(',');
                startSignalAnimation();
            });
        });

        $('.stop-btn').on('click', function () {
            stopSignalAnimation();
        });

        function startSignalAnimation() {
            if (interval) {
                clearInterval(interval);
            }
            interval = setInterval(() => {
                updateSignal();
            }, greenInternal + yellowInternal);
        }

        function stopSignalAnimation() {
            if (interval) {
                clearInterval(interval);
            }
            resetSignals();
        }

        function updateSignal() {
            resetSignals();
            const currentSignal = signals[currentSignalIndex];

            $('#signal' + currentSignal + '-red').addClass('inactive');
            $('#signal' + currentSignal + '-green').removeClass('inactive');
            setTimeout(() => {
                $('#signal' + currentSignal + '-green').addClass('inactive');
                $('#signal' + currentSignal + '-yellow').removeClass('inactive');
            }, greenInternal);
            currentSignalIndex = (currentSignalIndex + 1) % signals.length;
        }

        function resetSignals() {
            $('.light').addClass('inactive');
            $('.light.red').removeClass('inactive');
        }
    </script>
</body>
</html>
