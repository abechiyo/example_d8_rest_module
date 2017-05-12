<content-body>
  <script>
    console.log(opts);

    { this.root.innerHTML = opts.json.body[0].value }
  </script>
</content-body>

<!-- イベントタイトル -->
<event-title>
  <h1>{ opts.json.title }</h1>
</event-title>

<!-- イベント詳細 -->
<event-detail>
  <img src="{ opts.json.field_event_eye_catch }" class="fluid">
  <h3>場所:</h3>
  <p>{ opts.json.field_event_venue }</p>
  <h3>参加費:</h3>
  <p>{ opts.json.field_event_price }</p>
  <h3>開催日時:</h3>
  <p>{ opts.json.field_event_day }</p>
  <style>
  .fluid{ max-width: 50%; height: auto; }
  </style>
</event-detail>






