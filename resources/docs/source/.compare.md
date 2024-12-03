---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://devcloud.hotbox.today/docs/collection.json)

<!-- END_INFO -->

#general


<!-- START_8fcf8bf06eda291959b0c8a2a679f507 -->
## Show the dashboard.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET log-viewer`


<!-- END_8fcf8bf06eda291959b0c8a2a679f507 -->

<!-- START_15081fd356f32e98502fa21a2737815d -->
## List all logs.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer/logs" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET log-viewer/logs`


<!-- END_15081fd356f32e98502fa21a2737815d -->

<!-- START_82590949e3095f59d954041fb995ca98 -->
## Delete a log.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/log-viewer/logs/delete" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs/delete"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE log-viewer/logs/delete`


<!-- END_82590949e3095f59d954041fb995ca98 -->

<!-- START_8db2a7bd657e02e3967d6b65328c22d9 -->
## Show the log.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer/logs/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": "Log not found in this date [1]"
}
```

### HTTP Request
`GET log-viewer/logs/{date}`


<!-- END_8db2a7bd657e02e3967d6b65328c22d9 -->

<!-- START_6c595d3c570b8f53539b9a5918d283eb -->
## Download the log

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer/logs/1/download" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs/1/download"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET log-viewer/logs/{date}/download`


<!-- END_6c595d3c570b8f53539b9a5918d283eb -->

<!-- START_abff89e735a9c458227fc252a83922ac -->
## Filter the log entries by level.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer/logs/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": "Log not found in this date [1]"
}
```

### HTTP Request
`GET log-viewer/logs/{date}/{level}`


<!-- END_abff89e735a9c458227fc252a83922ac -->

<!-- START_441535d828b04c126a170a5949308df4 -->
## Show the log with the search query.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/log-viewer/logs/1/1/search" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/log-viewer/logs/1/1/search"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (302):

```json
null
```

### HTTP Request
`GET log-viewer/logs/{date}/{level}/search`


<!-- END_441535d828b04c126a170a5949308df4 -->

<!-- START_dc1a46498df72e9aafe15e7e324fcdfb -->
## __clockwork/{id}/extended
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/__clockwork/1/extended" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork/1/extended"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET __clockwork/{id}/extended`


<!-- END_dc1a46498df72e9aafe15e7e324fcdfb -->

<!-- START_f321e7ef878849ba9f117b781657de2a -->
## __clockwork/{id}/{direction?}/{count?}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/__clockwork/1//" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork/1//"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET __clockwork/{id}/{direction?}/{count?}`


<!-- END_f321e7ef878849ba9f117b781657de2a -->

<!-- START_d7436c1279fc7951f71bd08d03b7d432 -->
## __clockwork
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/__clockwork" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET __clockwork`


<!-- END_d7436c1279fc7951f71bd08d03b7d432 -->

<!-- START_e36bb2e75991fba5b6c2732a5665826b -->
## __clockwork/app
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/__clockwork/app" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork/app"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET __clockwork/app`


<!-- END_e36bb2e75991fba5b6c2732a5665826b -->

<!-- START_ae93168ca9a00596c39a28f85b34d499 -->
## __clockwork/assets/{path}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/__clockwork/assets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork/assets/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
{
    "message": ""
}
```

### HTTP Request
`GET __clockwork/assets/{path}`


<!-- END_ae93168ca9a00596c39a28f85b34d499 -->

<!-- START_e306f94114f4245b3fff5b2bbdc5f6e1 -->
## __clockwork/auth
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/__clockwork/auth" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/__clockwork/auth"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST __clockwork/auth`


<!-- END_e306f94114f4245b3fff5b2bbdc5f6e1 -->

<!-- START_7ebdd0ac8b3cd321e05382d1c06cd0b1 -->
## Get the key performance stats for the dashboard.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/stats" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/stats"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "jobsPerMinute": 0,
    "processes": 5,
    "queueWithMaxRuntime": null,
    "queueWithMaxThroughput": null,
    "failedJobs": 0,
    "recentJobs": 0,
    "status": "running",
    "wait": {
        "redis:migrate": 0
    },
    "periods": {
        "failedJobs": 60,
        "recentJobs": 60
    }
}
```

### HTTP Request
`GET horizon/api/stats`


<!-- END_7ebdd0ac8b3cd321e05382d1c06cd0b1 -->

<!-- START_5abc89804e68469f8260c0ded520f59c -->
## Get the current queue workload for the application.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/workload" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/workload"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "name": "default",
        "length": 0,
        "wait": 0,
        "processes": 1
    },
    {
        "name": "email",
        "length": 0,
        "wait": 0,
        "processes": 1
    },
    {
        "name": "housekeep",
        "length": 0,
        "wait": 0,
        "processes": 1
    },
    {
        "name": "metrc",
        "length": 0,
        "wait": 0,
        "processes": 1
    },
    {
        "name": "migrate",
        "length": 0,
        "wait": 0,
        "processes": 1
    }
]
```

### HTTP Request
`GET horizon/api/workload`


<!-- END_5abc89804e68469f8260c0ded520f59c -->

<!-- START_7d6f8da3e735f9175246fbab4b37610c -->
## Get all of the master supervisors and their underlying supervisors.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/masters" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/masters"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "apphotboxerpcom-vC8k": {
        "name": "apphotboxerpcom-vC8k",
        "pid": "1391",
        "status": "running",
        "supervisors": [
            {
                "name": "apphotboxerpcom-vC8k:supervisor-1",
                "master": "apphotboxerpcom-vC8k",
                "pid": "1637",
                "status": "running",
                "processes": {
                    "redis:default": 1,
                    "redis:email": 1,
                    "redis:housekeep": 1,
                    "redis:metrc": 1,
                    "redis:migrate": 1
                },
                "options": {
                    "balance": "simple",
                    "connection": "redis",
                    "queue": "default,email,housekeep,metrc,migrate",
                    "delay": "0",
                    "force": false,
                    "maxProcesses": 5,
                    "minProcesses": "1",
                    "maxTries": "3",
                    "memory": "128",
                    "nice": "0",
                    "name": "apphotboxerpcom-vC8k:supervisor-1",
                    "sleep": "3",
                    "timeout": "60"
                }
            }
        ]
    }
}
```

### HTTP Request
`GET horizon/api/masters`


<!-- END_7d6f8da3e735f9175246fbab4b37610c -->

<!-- START_3a653cb977489e73ed8798e5705defbf -->
## Get all of the monitored tags and their job counts.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/monitoring" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/monitoring"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/monitoring`


<!-- END_3a653cb977489e73ed8798e5705defbf -->

<!-- START_970935b1e560143fd003dd90a6f0b7b0 -->
## Start monitoring the given tag.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/horizon/api/monitoring" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/monitoring"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST horizon/api/monitoring`


<!-- END_970935b1e560143fd003dd90a6f0b7b0 -->

<!-- START_abd3993e15d364e7a2c79c9caa73a862 -->
## Paginate the jobs for a given tag.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/monitoring/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/monitoring/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/monitoring/{tag}`


<!-- END_abd3993e15d364e7a2c79c9caa73a862 -->

<!-- START_9f62e45bc2a894b92554c1406f487f03 -->
## Stop monitoring the given tag.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/horizon/api/monitoring/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/monitoring/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE horizon/api/monitoring/{tag}`


<!-- END_9f62e45bc2a894b92554c1406f487f03 -->

<!-- START_9808e9d7d776f039d57c72f052e6e8cc -->
## Get all of the measured jobs.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/metrics/jobs" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/metrics/jobs"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/jobs`


<!-- END_9808e9d7d776f039d57c72f052e6e8cc -->

<!-- START_dbb28dc188d668f7fa836ee5bc43e243 -->
## Get metrics for a given job.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/metrics/jobs/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/metrics/jobs/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/jobs/{id}`


<!-- END_dbb28dc188d668f7fa836ee5bc43e243 -->

<!-- START_ca0a10e3b27a3c5820831f79ab403f78 -->
## Get all of the measured queues.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/metrics/queues" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/metrics/queues"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/queues`


<!-- END_ca0a10e3b27a3c5820831f79ab403f78 -->

<!-- START_7a3c56bda1e4b728cf5a5691ee989766 -->
## Get metrics for a given queue.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/metrics/queues/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/metrics/queues/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/metrics/queues/{id}`


<!-- END_7a3c56bda1e4b728cf5a5691ee989766 -->

<!-- START_c34fa16bca5eb044bd9b7d7643c3376a -->
## Get all of the recent jobs.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/jobs/recent" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/jobs/recent"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/jobs/recent`


<!-- END_c34fa16bca5eb044bd9b7d7643c3376a -->

<!-- START_73a5f0771b8fdd710e2b547f24f1b308 -->
## Get all of the failed jobs.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/jobs/failed" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/jobs/failed"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "jobs": [],
    "total": 0
}
```

### HTTP Request
`GET horizon/api/jobs/failed`


<!-- END_73a5f0771b8fdd710e2b547f24f1b308 -->

<!-- START_25959bfc2e37e26b5875453cbf717c3f -->
## Get a failed job instance.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/api/jobs/failed/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/jobs/failed/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[]
```

### HTTP Request
`GET horizon/api/jobs/failed/{id}`


<!-- END_25959bfc2e37e26b5875453cbf717c3f -->

<!-- START_b69e44e22af794a2060e89edd04f0600 -->
## Retry a failed job.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/horizon/api/jobs/retry/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/api/jobs/retry/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST horizon/api/jobs/retry/{id}`


<!-- END_b69e44e22af794a2060e89edd04f0600 -->

<!-- START_fb7b7b4614d0392062e423beed14f31f -->
## Single page application catch-all route.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/horizon/" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/horizon/"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET horizon/{view?}`


<!-- END_fb7b7b4614d0392062e423beed14f31f -->

<!-- START_0c068b4037fb2e47e71bd44bd36e3e2a -->
## Authorize a client to access the user&#039;s account.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/oauth/authorize" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/authorize"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET oauth/authorize`


<!-- END_0c068b4037fb2e47e71bd44bd36e3e2a -->

<!-- START_e48cc6a0b45dd21b7076ab2c03908687 -->
## Approve the authorization request.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/oauth/authorize" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/authorize"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST oauth/authorize`


<!-- END_e48cc6a0b45dd21b7076ab2c03908687 -->

<!-- START_de5d7581ef1275fce2a229b6b6eaad9c -->
## Deny the authorization request.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/oauth/authorize" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/authorize"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE oauth/authorize`


<!-- END_de5d7581ef1275fce2a229b6b6eaad9c -->

<!-- START_a09d20357336aa979ecd8e3972ac9168 -->
## Authorize a client to access the user&#039;s account.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/oauth/token" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/token"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST oauth/token`


<!-- END_a09d20357336aa979ecd8e3972ac9168 -->

<!-- START_d6a56149547e03307199e39e03e12d1c -->
## Get all of the authorized tokens for the authenticated user.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/oauth/tokens" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/tokens"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET oauth/tokens`


<!-- END_d6a56149547e03307199e39e03e12d1c -->

<!-- START_a9a802c25737cca5324125e5f60b72a5 -->
## Delete the given token.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/oauth/tokens/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/tokens/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE oauth/tokens/{token_id}`


<!-- END_a9a802c25737cca5324125e5f60b72a5 -->

<!-- START_abe905e69f5d002aa7d26f433676d623 -->
## Get a fresh transient token cookie for the authenticated user.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/oauth/token/refresh" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/token/refresh"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST oauth/token/refresh`


<!-- END_abe905e69f5d002aa7d26f433676d623 -->

<!-- START_babcfe12d87b8708f5985e9d39ba8f2c -->
## Get all of the clients for the authenticated user.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/oauth/clients" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/clients"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET oauth/clients`


<!-- END_babcfe12d87b8708f5985e9d39ba8f2c -->

<!-- START_9eabf8d6e4ab449c24c503fcb42fba82 -->
## Store a new client.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/oauth/clients" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/clients"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST oauth/clients`


<!-- END_9eabf8d6e4ab449c24c503fcb42fba82 -->

<!-- START_784aec390a455073fc7464335c1defa1 -->
## Update the given client.

> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/oauth/clients/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/clients/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT oauth/clients/{client_id}`


<!-- END_784aec390a455073fc7464335c1defa1 -->

<!-- START_1f65a511dd86ba0541d7ba13ca57e364 -->
## Delete the given client.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/oauth/clients/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/clients/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE oauth/clients/{client_id}`


<!-- END_1f65a511dd86ba0541d7ba13ca57e364 -->

<!-- START_9e281bd3a1eb1d9eb63190c8effb607c -->
## Get all of the available scopes for the application.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/oauth/scopes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/scopes"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET oauth/scopes`


<!-- END_9e281bd3a1eb1d9eb63190c8effb607c -->

<!-- START_9b2a7699ce6214a79e0fd8107f8b1c9e -->
## Get all of the personal access tokens for the authenticated user.

> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/oauth/personal-access-tokens" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/personal-access-tokens"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET oauth/personal-access-tokens`


<!-- END_9b2a7699ce6214a79e0fd8107f8b1c9e -->

<!-- START_a8dd9c0a5583742e671711f9bb3ee406 -->
## Create a new personal access token for the user.

> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/oauth/personal-access-tokens" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/personal-access-tokens"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST oauth/personal-access-tokens`


<!-- END_a8dd9c0a5583742e671711f9bb3ee406 -->

<!-- START_bae65df80fd9d72a01439241a9ea20d0 -->
## Delete the given token.

> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/oauth/personal-access-tokens/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/oauth/personal-access-tokens/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE oauth/personal-access-tokens/{token_id}`


<!-- END_bae65df80fd9d72a01439241a9ea20d0 -->

<!-- START_1b87298cbf6aa387ec1a41c3231ba0dd -->
## api/v1/admin/auth/servicelogs
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/servicelogs`


<!-- END_1b87298cbf6aa387ec1a41c3231ba0dd -->

<!-- START_4b529b40a3297414e9011df16e9c7439 -->
## api/v1/admin/auth/servicelogs/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/servicelogs/export/{type}`


<!-- END_4b529b40a3297414e9011df16e9c7439 -->

<!-- START_35af2140fc6d2abc6f508fdf2d94a9e2 -->
## api/v1/admin/auth/servicelogs/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/servicelogs/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/servicelogs/{id}`


<!-- END_35af2140fc6d2abc6f508fdf2d94a9e2 -->

<!-- START_4f90560c67927c147cde5c542f0b3da6 -->
## api/v1/admin/dispensary/campaigns
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/campaigns`


<!-- END_4f90560c67927c147cde5c542f0b3da6 -->

<!-- START_d8d7ebe96802c527d7b348ca2910afe2 -->
## api/v1/admin/dispensary/campaigns/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/campaigns/export/{type}`


<!-- END_d8d7ebe96802c527d7b348ca2910afe2 -->

<!-- START_942e4acf8649f83fd6fdf80dadcfb372 -->
## api/v1/admin/dispensary/campaigns/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/campaigns/{id}/export/{type}`


<!-- END_942e4acf8649f83fd6fdf80dadcfb372 -->

<!-- START_b5e72e2523d88adf3bbdc464e31ddfbc -->
## api/v1/admin/dispensary/campaigns/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/campaigns/{id}`


<!-- END_b5e72e2523d88adf3bbdc464e31ddfbc -->

<!-- START_efd2afe1d27ed9973e819e4a055a7a60 -->
## api/v1/admin/dispensary/campaigns
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/campaigns`


<!-- END_efd2afe1d27ed9973e819e4a055a7a60 -->

<!-- START_dd7ebcaa16d0578725caa4f9ce73df37 -->
## api/v1/admin/dispensary/campaigns/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/campaigns/{id}`


<!-- END_dd7ebcaa16d0578725caa4f9ce73df37 -->

<!-- START_96e98594da59394c07ca0c8125fb2c52 -->
## api/v1/admin/dispensary/campaigns/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/campaigns/{id}`


<!-- END_96e98594da59394c07ca0c8125fb2c52 -->

<!-- START_b826af848293836a15b7b6a991eb7299 -->
## api/v1/admin/dispensary/campaigns/requeue/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/requeue/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/campaigns/requeue/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/campaigns/requeue/{id}`


<!-- END_b826af848293836a15b7b6a991eb7299 -->

<!-- START_966b1867862e8efcb92eaf08defd7f91 -->
## api/v1/admin/dispensary/categories
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories`


<!-- END_966b1867862e8efcb92eaf08defd7f91 -->

<!-- START_dab3d97e4ea69468b022ad80a53624e0 -->
## api/v1/admin/dispensary/categories/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories/batch`


<!-- END_dab3d97e4ea69468b022ad80a53624e0 -->

<!-- START_ac4f64af9220f2684398873a6547336f -->
## api/v1/admin/dispensary/categories/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories/export/{type}`


<!-- END_ac4f64af9220f2684398873a6547336f -->

<!-- START_01f7c22d4ed60b4f2e2f0693d03757ff -->
## api/v1/admin/dispensary/categories/syncMetrc
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/syncMetrc" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/syncMetrc"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories/syncMetrc`


<!-- END_01f7c22d4ed60b4f2e2f0693d03757ff -->

<!-- START_c91510eb6dbd61e6f69ea9c54aa18f7e -->
## api/v1/admin/dispensary/categories/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories/{id}/export/{type}`


<!-- END_c91510eb6dbd61e6f69ea9c54aa18f7e -->

<!-- START_f45aea1e900353e02fca7c7052a30988 -->
## api/v1/admin/dispensary/categories/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/categories/{id}`


<!-- END_f45aea1e900353e02fca7c7052a30988 -->

<!-- START_2de096684c37869e3ac7211f5bdc14cb -->
## api/v1/admin/dispensary/categories/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/categories/batch/{type}`


<!-- END_2de096684c37869e3ac7211f5bdc14cb -->

<!-- START_65248774aeb8d6c25a472b6ae426bea3 -->
## api/v1/admin/dispensary/categories
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/categories`


<!-- END_65248774aeb8d6c25a472b6ae426bea3 -->

<!-- START_3b640682c45b3880d0f8d08cc44dc1e2 -->
## api/v1/admin/dispensary/categories/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/categories/{id}`


<!-- END_3b640682c45b3880d0f8d08cc44dc1e2 -->

<!-- START_2ece0ba8bd61eaa39307c5b2ff913f94 -->
## api/v1/admin/dispensary/categories/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/categories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/categories/{id}`


<!-- END_2ece0ba8bd61eaa39307c5b2ff913f94 -->

<!-- START_cf9d7ba6b6983b6b88db796462c05aed -->
## api/v1/admin/dispensary/customers
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers`


<!-- END_cf9d7ba6b6983b6b88db796462c05aed -->

<!-- START_db17705d17c638109b36f71649717399 -->
## api/v1/admin/dispensary/customers/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers/batch`


<!-- END_db17705d17c638109b36f71649717399 -->

<!-- START_912380fb77e2beb15785cdf66a975594 -->
## api/v1/admin/dispensary/customers/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers/export/{type}`


<!-- END_912380fb77e2beb15785cdf66a975594 -->

<!-- START_fc67a99963e0a0a81fe047cd899571c5 -->
## api/v1/admin/dispensary/customers/smurfname
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/smurfname" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/smurfname"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers/smurfname`


<!-- END_fc67a99963e0a0a81fe047cd899571c5 -->

<!-- START_d11bb0b34dd2312c151e3ac83b551ec6 -->
## api/v1/admin/dispensary/customers/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers/{id}/export/{type}`


<!-- END_d11bb0b34dd2312c151e3ac83b551ec6 -->

<!-- START_44932fb9c02a8a0ea23a85eafab3f6cb -->
## api/v1/admin/dispensary/customers/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customers/{id}`


<!-- END_44932fb9c02a8a0ea23a85eafab3f6cb -->

<!-- START_633f8d803740f25ec32af7529c506f4a -->
## api/v1/admin/dispensary/customers/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/customers/batch/{type}`


<!-- END_633f8d803740f25ec32af7529c506f4a -->

<!-- START_4c1e99ff5e7b8799b9db6aae7b41db5d -->
## api/v1/admin/dispensary/customers
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/customers`


<!-- END_4c1e99ff5e7b8799b9db6aae7b41db5d -->

<!-- START_8da0113fea86ff0db194e413f02776aa -->
## api/v1/admin/dispensary/customers/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/customers/{id}`


<!-- END_8da0113fea86ff0db194e413f02776aa -->

<!-- START_a141355b42c58a11654e8cb6472c616e -->
## api/v1/admin/dispensary/customers/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/customers/{id}`


<!-- END_a141355b42c58a11654e8cb6472c616e -->

<!-- START_9d5468818f83591be6c17165625146f4 -->
## api/v1/admin/dispensary/customersqueue
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customersqueue`


<!-- END_9d5468818f83591be6c17165625146f4 -->

<!-- START_a0188d9c151f0bfd65f036d112af4e43 -->
## api/v1/admin/dispensary/customersqueue/{type}/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customersqueue/{type}/{id}`


<!-- END_a0188d9c151f0bfd65f036d112af4e43 -->

<!-- START_a72f56af9ccf30b11d80836be51f7199 -->
## api/v1/admin/dispensary/customersqueue
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customersqueue"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/customersqueue`


<!-- END_a72f56af9ccf30b11d80836be51f7199 -->

<!-- START_d29e26e8db33f5f9e10e09dab2f4a0ac -->
## api/v1/admin/dispensary/discounts
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/discounts`


<!-- END_d29e26e8db33f5f9e10e09dab2f4a0ac -->

<!-- START_671b347135271378db1ee42252fea6e8 -->
## api/v1/admin/dispensary/discounts/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/discounts/batch`


<!-- END_671b347135271378db1ee42252fea6e8 -->

<!-- START_ed129c77a3619b9f05a166ac46a1f89d -->
## api/v1/admin/dispensary/discounts/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/discounts/export/{type}`


<!-- END_ed129c77a3619b9f05a166ac46a1f89d -->

<!-- START_b521f00ae22f3dad160223e51434cea3 -->
## api/v1/admin/dispensary/discounts/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/discounts/{id}/export/{type}`


<!-- END_b521f00ae22f3dad160223e51434cea3 -->

<!-- START_c77e47bb8418fa7375c96dad0420c7bd -->
## api/v1/admin/dispensary/discounts/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/discounts/{id}`


<!-- END_c77e47bb8418fa7375c96dad0420c7bd -->

<!-- START_93e397aba08b7a89f1358f031b554e8c -->
## api/v1/admin/dispensary/discounts/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/discounts/batch/{type}`


<!-- END_93e397aba08b7a89f1358f031b554e8c -->

<!-- START_9536815fd63b223ad579bf4dc25280e4 -->
## api/v1/admin/dispensary/discounts
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/discounts`


<!-- END_9536815fd63b223ad579bf4dc25280e4 -->

<!-- START_24a07bffab7f3b114f9e13b5d3aec050 -->
## api/v1/admin/dispensary/discounts/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/discounts/{id}`


<!-- END_24a07bffab7f3b114f9e13b5d3aec050 -->

<!-- START_e86ed77c90bea2ca548ce7ae496d9875 -->
## api/v1/admin/dispensary/discounts/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/discounts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/discounts/{id}`


<!-- END_e86ed77c90bea2ca548ce7ae496d9875 -->

<!-- START_2220954a4d9b4c74aa9c793f864fb151 -->
## api/v1/admin/dispensary/drawers
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers`


<!-- END_2220954a4d9b4c74aa9c793f864fb151 -->

<!-- START_19c688876fb0b34f1ff0bb92c75cca6b -->
## api/v1/admin/dispensary/drawers/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers/batch`


<!-- END_19c688876fb0b34f1ff0bb92c75cca6b -->

<!-- START_c509934fe6d915e3b3f76eb884a0df23 -->
## api/v1/admin/dispensary/drawers/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers/export/{type}`


<!-- END_c509934fe6d915e3b3f76eb884a0df23 -->

<!-- START_39fb1afaa527d60a85c84c9c93f31697 -->
## api/v1/admin/dispensary/drawers/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers/{id}/export/{type}`


<!-- END_39fb1afaa527d60a85c84c9c93f31697 -->

<!-- START_80434400b5fe1773819eab6a989c4bc2 -->
## api/v1/admin/dispensary/drawers/getOpenDrawer
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/getOpenDrawer" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/getOpenDrawer"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers/getOpenDrawer`


<!-- END_80434400b5fe1773819eab6a989c4bc2 -->

<!-- START_cd5162417ae721a3f2d909a8b38d7d0e -->
## api/v1/admin/dispensary/drawers/{id}/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/drawers/{id}/{type}`


<!-- END_cd5162417ae721a3f2d909a8b38d7d0e -->

<!-- START_49d02afc081303439ce1de8481f91359 -->
## api/v1/admin/dispensary/drawers/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/drawers/{id}`


<!-- END_49d02afc081303439ce1de8481f91359 -->

<!-- START_a99aba092a0e40b41df75edeebccc744 -->
## api/v1/admin/dispensary/drawers/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/drawers/batch/{type}`


<!-- END_a99aba092a0e40b41df75edeebccc744 -->

<!-- START_9ba45134dc66d6069476b9171a4440f8 -->
## api/v1/admin/dispensary/drawers
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/drawers`


<!-- END_9ba45134dc66d6069476b9171a4440f8 -->

<!-- START_38dda799307b67aa0346598e21235c6a -->
## api/v1/admin/dispensary/drawers/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/drawers/{id}`


<!-- END_38dda799307b67aa0346598e21235c6a -->

<!-- START_b195fd5ab3db06ae666ef68d8d08b4ce -->
## api/v1/admin/dispensary/drawers/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/drawers/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/drawers/{id}`


<!-- END_b195fd5ab3db06ae666ef68d8d08b4ce -->

<!-- START_d5bad593f93ef9e205d4a51f116028a1 -->
## api/v1/admin/dispensary/groups
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/groups`


<!-- END_d5bad593f93ef9e205d4a51f116028a1 -->

<!-- START_c512dad1d6355a0639eef20427dd5491 -->
## api/v1/admin/dispensary/groups/filter
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/filter" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/filter"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/groups/filter`


<!-- END_c512dad1d6355a0639eef20427dd5491 -->

<!-- START_bad74643682d9e2740981fe18aabec89 -->
## api/v1/admin/dispensary/groups/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/groups/export/{type}`


<!-- END_bad74643682d9e2740981fe18aabec89 -->

<!-- START_1364e7aecd18660dddf3713483295a23 -->
## api/v1/admin/dispensary/groups/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/groups/{id}/export/{type}`


<!-- END_1364e7aecd18660dddf3713483295a23 -->

<!-- START_91b660e2d63be8dcda8e25e044e985aa -->
## api/v1/admin/dispensary/groups/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/groups/{id}`


<!-- END_91b660e2d63be8dcda8e25e044e985aa -->

<!-- START_e50e3ab329c703cd8b8c7e8765784382 -->
## api/v1/admin/dispensary/groups
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/groups`


<!-- END_e50e3ab329c703cd8b8c7e8765784382 -->

<!-- START_17850ef135be652ef39cf5b1abdebdf6 -->
## api/v1/admin/dispensary/groups/filter
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/filter" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/filter"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/groups/filter`


<!-- END_17850ef135be652ef39cf5b1abdebdf6 -->

<!-- START_16d5d0358515c46ee5d74cd58b115645 -->
## api/v1/admin/dispensary/groups/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/groups/{id}`


<!-- END_16d5d0358515c46ee5d74cd58b115645 -->

<!-- START_f3ddde6fc0d7f9354bf41da88c5d20dc -->
## api/v1/admin/dispensary/groups/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/groups/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/groups/{id}`


<!-- END_f3ddde6fc0d7f9354bf41da88c5d20dc -->

<!-- START_b15118777fea23623ad33e238c5f8e8f -->
## api/v1/admin/dispensary/customergroups
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/customergroups" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/customergroups"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/customergroups`


<!-- END_b15118777fea23623ad33e238c5f8e8f -->

<!-- START_e638661478de7d6aade782ab28c18b0f -->
## api/v1/admin/dispensary/inventories
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories`


<!-- END_e638661478de7d6aade782ab28c18b0f -->

<!-- START_e469d67813e38f8ffe23c983c145ca0b -->
## api/v1/admin/dispensary/inventories/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories/batch`


<!-- END_e469d67813e38f8ffe23c983c145ca0b -->

<!-- START_eb5b615cec9c39205cd531d508961f06 -->
## api/v1/admin/dispensary/inventories/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories/export/{type}`


<!-- END_eb5b615cec9c39205cd531d508961f06 -->

<!-- START_5991a8c6b6c89868a6a5f2a7d9a3019e -->
## api/v1/admin/dispensary/inventories/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories/{id}/export/{type}`


<!-- END_5991a8c6b6c89868a6a5f2a7d9a3019e -->

<!-- START_5efb53d5507358b743fefa303380e08c -->
## api/v1/admin/dispensary/inventories/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories/{id}`


<!-- END_5efb53d5507358b743fefa303380e08c -->

<!-- START_13ca6658a99d610d4443c47d15fac056 -->
## api/v1/admin/dispensary/inventories/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/inventories/batch/{type}`


<!-- END_13ca6658a99d610d4443c47d15fac056 -->

<!-- START_b72a2b102eafff0c7318aa0b876dcb0a -->
## api/v1/admin/dispensary/inventories/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/inventories/{id}`


<!-- END_b72a2b102eafff0c7318aa0b876dcb0a -->

<!-- START_966e828e18b678630e96789d905e477f -->
## api/v1/admin/dispensary/inventories/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/inventories/{id}`


<!-- END_966e828e18b678630e96789d905e477f -->

<!-- START_3c2390cee017113d389de1c23d9dc9a3 -->
## api/v1/admin/dispensary/inventories/label/schema
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/label/schema" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/label/schema"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/inventories/label/schema`


<!-- END_3c2390cee017113d389de1c23d9dc9a3 -->

<!-- START_85c3cefe7b9a039effac9a7555dfd315 -->
## api/v1/admin/dispensary/inventories/label/render
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/label/render" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/inventories/label/render"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/inventories/label/render`


<!-- END_85c3cefe7b9a039effac9a7555dfd315 -->

<!-- START_b14af4af7e5ff4c2eada52a35d3acb25 -->
## api/v1/admin/dispensary/pricesets
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/pricesets`


<!-- END_b14af4af7e5ff4c2eada52a35d3acb25 -->

<!-- START_474f9ca2b247cf7034994a92f7f7795c -->
## api/v1/admin/dispensary/pricesets/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/pricesets/export/{type}`


<!-- END_474f9ca2b247cf7034994a92f7f7795c -->

<!-- START_9fae50c71552b67ab16bf0e0d8e5735d -->
## api/v1/admin/dispensary/pricesets/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/pricesets/{id}/export/{type}`


<!-- END_9fae50c71552b67ab16bf0e0d8e5735d -->

<!-- START_82a8254ffc13fe063e11138df40b91a2 -->
## api/v1/admin/dispensary/pricesets/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/pricesets/{id}`


<!-- END_82a8254ffc13fe063e11138df40b91a2 -->

<!-- START_7cb52f91858e02a17613569bb36d78cf -->
## api/v1/admin/dispensary/pricesets
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/pricesets`


<!-- END_7cb52f91858e02a17613569bb36d78cf -->

<!-- START_d5a2054f8674f44147caee0b2120638e -->
## api/v1/admin/dispensary/pricesets/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/pricesets/{id}`


<!-- END_d5a2054f8674f44147caee0b2120638e -->

<!-- START_950471e1e26ee44e6f6ede0ff11e9b64 -->
## api/v1/admin/dispensary/pricesets/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/pricesets/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/pricesets/{id}`


<!-- END_950471e1e26ee44e6f6ede0ff11e9b64 -->

<!-- START_c85b2b8732348fea97d0c287883edf7c -->
## api/v1/admin/dispensary/products
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/products" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/products`


<!-- END_c85b2b8732348fea97d0c287883edf7c -->

<!-- START_f4378f700093c48c9411b69fe07ef743 -->
## api/v1/admin/dispensary/products/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/products/batch`


<!-- END_f4378f700093c48c9411b69fe07ef743 -->

<!-- START_9b6e4f0f353e00878efeb89194936a5f -->
## api/v1/admin/dispensary/products/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/products/export/{type}`


<!-- END_9b6e4f0f353e00878efeb89194936a5f -->

<!-- START_bf3299109b626077c081d9f859caaa9e -->
## api/v1/admin/dispensary/products/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/products/{id}/export/{type}`


<!-- END_bf3299109b626077c081d9f859caaa9e -->

<!-- START_c8a6a7cb50141953b8288634b3c1479c -->
## api/v1/admin/dispensary/products/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/products/{id}`


<!-- END_c8a6a7cb50141953b8288634b3c1479c -->

<!-- START_13652d860775398485bf90e2a24553be -->
## api/v1/admin/dispensary/products/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/products/batch/{type}`


<!-- END_13652d860775398485bf90e2a24553be -->

<!-- START_a938d1ef1e91a73618feac678a9f3ee9 -->
## api/v1/admin/dispensary/products
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/products`


<!-- END_a938d1ef1e91a73618feac678a9f3ee9 -->

<!-- START_1f38ffa815db31d7dec227deb2610c32 -->
## api/v1/admin/dispensary/products/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/products/{id}`


<!-- END_1f38ffa815db31d7dec227deb2610c32 -->

<!-- START_878494f659fcbad6b7b43453d272b4e7 -->
## api/v1/admin/dispensary/products/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/products/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/products/{id}`


<!-- END_878494f659fcbad6b7b43453d272b4e7 -->

<!-- START_0a471992d1903fdd1ed2af57e041d1cc -->
## api/v1/admin/dispensary/receivings
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings`


<!-- END_0a471992d1903fdd1ed2af57e041d1cc -->

<!-- START_5ffe4285e6b823e04b9e36e7d09b46db -->
## api/v1/admin/dispensary/receivings/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/export/{type}`


<!-- END_5ffe4285e6b823e04b9e36e7d09b46db -->

<!-- START_5caa3269d3cb4905c78cec2f4e9875fe -->
## api/v1/admin/dispensary/receivings/transfers/import/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/transfers/import/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/transfers/import/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/transfers/import/{id}`


<!-- END_5caa3269d3cb4905c78cec2f4e9875fe -->

<!-- START_13bbad74aa1262d5189fbeb12a63a892 -->
## api/v1/admin/dispensary/receivings/transfers
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/transfers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/transfers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/transfers`


<!-- END_13bbad74aa1262d5189fbeb12a63a892 -->

<!-- START_fa16b2cc8d85d79c426ad4c77d9ce6ff -->
## api/v1/admin/dispensary/receivings/syncTransfers
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/syncTransfers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/syncTransfers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/syncTransfers`


<!-- END_fa16b2cc8d85d79c426ad4c77d9ce6ff -->

<!-- START_dd0f6c387552cde35cba0412242f7a45 -->
## api/v1/admin/dispensary/receivings/packages
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/packages" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/packages"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/packages`


<!-- END_dd0f6c387552cde35cba0412242f7a45 -->

<!-- START_2c51639438a17af311c5ebcc3f970e2f -->
## api/v1/admin/dispensary/receivings/syncPackages
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/syncPackages" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/syncPackages"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/syncPackages`


<!-- END_2c51639438a17af311c5ebcc3f970e2f -->

<!-- START_93dc831ccf4d5fcaada4029bee58120e -->
## api/v1/admin/dispensary/receivings/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/{id}/export/{type}`


<!-- END_93dc831ccf4d5fcaada4029bee58120e -->

<!-- START_0d11f994e03fc6e4e5d9fa9fdbcb63b9 -->
## api/v1/admin/dispensary/receivings/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/receivings/{id}`


<!-- END_0d11f994e03fc6e4e5d9fa9fdbcb63b9 -->

<!-- START_b200421911e103b25c4f39b6406f5615 -->
## api/v1/admin/dispensary/receivings
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/receivings`


<!-- END_b200421911e103b25c4f39b6406f5615 -->

<!-- START_0415d38c230faf5342a412fa3c2eb90c -->
## api/v1/admin/dispensary/receivings/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/receivings/{id}`


<!-- END_0415d38c230faf5342a412fa3c2eb90c -->

<!-- START_b08345f3bd2553d14fa394e30f8ea4b8 -->
## api/v1/admin/dispensary/receivings/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/receivings/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/receivings/{id}`


<!-- END_b08345f3bd2553d14fa394e30f8ea4b8 -->

<!-- START_4b96c2395472cc766e0a04a1dcf4cd0d -->
## api/v1/admin/dispensary/rewards
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/rewards`


<!-- END_4b96c2395472cc766e0a04a1dcf4cd0d -->

<!-- START_8424eb3d5fc8415e78f2b44077de5434 -->
## api/v1/admin/dispensary/rewards/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/rewards/export/{type}`


<!-- END_8424eb3d5fc8415e78f2b44077de5434 -->

<!-- START_7c775f713f207084e224537717e3337e -->
## api/v1/admin/dispensary/rewards/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/rewards/{id}/export/{type}`


<!-- END_7c775f713f207084e224537717e3337e -->

<!-- START_475e18d4bf1c1499c4bed20b1cb60fb0 -->
## api/v1/admin/dispensary/rewards/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/rewards/{id}`


<!-- END_475e18d4bf1c1499c4bed20b1cb60fb0 -->

<!-- START_a4aed381c72b9aeeee2f07da7cfd7799 -->
## api/v1/admin/dispensary/rewards
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/rewards`


<!-- END_a4aed381c72b9aeeee2f07da7cfd7799 -->

<!-- START_7c5d0a5c22dacf6523df57612d1e5464 -->
## api/v1/admin/dispensary/rewards/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/rewards/{id}`


<!-- END_7c5d0a5c22dacf6523df57612d1e5464 -->

<!-- START_ecc166fe180fd8a068db7c7d1f45213c -->
## api/v1/admin/dispensary/rewards/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewards/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/rewards/{id}`


<!-- END_ecc166fe180fd8a068db7c7d1f45213c -->

<!-- START_2d0b5932788ae48ca3c90a8565766efc -->
## api/v1/admin/dispensary/rewardstriggers
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewardstriggers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewardstriggers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/rewardstriggers`


<!-- END_2d0b5932788ae48ca3c90a8565766efc -->

<!-- START_b3f47ed539ec22e5f1cf41b84e1a6346 -->
## api/v1/admin/dispensary/rewardstriggers
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewardstriggers" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/rewardstriggers"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/rewardstriggers`


<!-- END_b3f47ed539ec22e5f1cf41b84e1a6346 -->

<!-- START_c8b4af1941abc63a99a4458bf4196716 -->
## api/v1/admin/dispensary/sales
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales`


<!-- END_c8b4af1941abc63a99a4458bf4196716 -->

<!-- START_c2019f66e3da77a8171811032daf36eb -->
## api/v1/admin/dispensary/sales/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales/batch`


<!-- END_c2019f66e3da77a8171811032daf36eb -->

<!-- START_8604ef1b9606c267345b9614388672c9 -->
## api/v1/admin/dispensary/sales/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales/export/{type}`


<!-- END_8604ef1b9606c267345b9614388672c9 -->

<!-- START_de78310c9ad4d43e0cba2745b651bb6e -->
## api/v1/admin/dispensary/sales/{id}/void
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/void" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/void"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales/{id}/void`


<!-- END_de78310c9ad4d43e0cba2745b651bb6e -->

<!-- START_1482baead957cbc5a6d62f0e78451f71 -->
## api/v1/admin/dispensary/sales/{id}/return
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/return" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/return"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/sales/{id}/return`


<!-- END_1482baead957cbc5a6d62f0e78451f71 -->

<!-- START_fed9a5a895a2cb85e38e0ef94588c1c1 -->
## api/v1/admin/dispensary/sales/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales/{id}/export/{type}`


<!-- END_fed9a5a895a2cb85e38e0ef94588c1c1 -->

<!-- START_dbe7cd179e15aec2f1f70eee902cee57 -->
## api/v1/admin/dispensary/sales/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/sales/{id}`


<!-- END_dbe7cd179e15aec2f1f70eee902cee57 -->

<!-- START_cf644eff435a65f23008ee4a202ac4c3 -->
## api/v1/admin/dispensary/sales/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/sales/batch/{type}`


<!-- END_cf644eff435a65f23008ee4a202ac4c3 -->

<!-- START_4cd1fa05de6652a0484a90597382048f -->
## api/v1/admin/dispensary/sales/{id}/payment
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/payment" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1/payment"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/sales/{id}/payment`


<!-- END_4cd1fa05de6652a0484a90597382048f -->

<!-- START_a37a3e4d722543115e95d6e2525217e9 -->
## api/v1/admin/dispensary/sales
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/sales`


<!-- END_a37a3e4d722543115e95d6e2525217e9 -->

<!-- START_15eec994bceea7cf5ba5ec8aebb640ad -->
## api/v1/admin/dispensary/sales/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/sales/{id}`


<!-- END_15eec994bceea7cf5ba5ec8aebb640ad -->

<!-- START_685aeecf635731f55e07b7b816227e0d -->
## api/v1/admin/dispensary/sales/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/sales/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/sales/{id}`


<!-- END_685aeecf635731f55e07b7b816227e0d -->

<!-- START_4c8aa2a35c7f3ddbae3010fe9e13223a -->
## api/v1/admin/dispensary/taxes
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/taxes`


<!-- END_4c8aa2a35c7f3ddbae3010fe9e13223a -->

<!-- START_aa6a0d5ceb3774b3363d531240ee690f -->
## api/v1/admin/dispensary/taxes/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/taxes/export/{type}`


<!-- END_aa6a0d5ceb3774b3363d531240ee690f -->

<!-- START_a52fd92def17c9bd20da8b6dee2151b2 -->
## api/v1/admin/dispensary/taxes/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/taxes/{id}/export/{type}`


<!-- END_a52fd92def17c9bd20da8b6dee2151b2 -->

<!-- START_0ed2c730e0e4135bffb22a523c9cc0ba -->
## api/v1/admin/dispensary/taxes/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/taxes/{id}`


<!-- END_0ed2c730e0e4135bffb22a523c9cc0ba -->

<!-- START_89fe64e790158f91c8e65b4c02e200c2 -->
## api/v1/admin/dispensary/taxes
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/taxes`


<!-- END_89fe64e790158f91c8e65b4c02e200c2 -->

<!-- START_cf74e94c81bfa7dedaf8f98683a53095 -->
## api/v1/admin/dispensary/taxes/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/taxes/{id}`


<!-- END_cf74e94c81bfa7dedaf8f98683a53095 -->

<!-- START_472751f6f1f47a72f0ec66b820b7f9ad -->
## api/v1/admin/dispensary/taxes/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxes/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/dispensary/taxes/{id}`


<!-- END_472751f6f1f47a72f0ec66b820b7f9ad -->

<!-- START_e60002f6da35215cd8cd7de031415713 -->
## api/v1/admin/dispensary/taxrates/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/dispensary/taxrates/{id}`


<!-- END_e60002f6da35215cd8cd7de031415713 -->

<!-- START_ab422a31e19cf20eec9031ca0556c8a8 -->
## api/v1/admin/dispensary/taxrates
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/dispensary/taxrates`


<!-- END_ab422a31e19cf20eec9031ca0556c8a8 -->

<!-- START_e857b91bf5a270d9013b402486ed2e76 -->
## api/v1/admin/dispensary/taxrates/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/dispensary/taxrates/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/dispensary/taxrates/{id}`


<!-- END_e857b91bf5a270d9013b402486ed2e76 -->

<!-- START_9c516284c33b396cf5fa5e482ce1975a -->
## api/v1/admin/grow/harvests
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/harvests" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/harvests"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/harvests`


<!-- END_9c516284c33b396cf5fa5e482ce1975a -->

<!-- START_fb762697761df02cc8c06a45f7bcaef0 -->
## api/v1/admin/grow/harvests/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/harvests/batch`


<!-- END_fb762697761df02cc8c06a45f7bcaef0 -->

<!-- START_197adf85abfc7f4fdf6abb0c771754e2 -->
## api/v1/admin/grow/harvests/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/harvests/export/{type}`


<!-- END_197adf85abfc7f4fdf6abb0c771754e2 -->

<!-- START_48f25c1c763fbfffb38f49ba6ab0581a -->
## api/v1/admin/grow/harvests/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/harvests/{id}/export/{type}`


<!-- END_48f25c1c763fbfffb38f49ba6ab0581a -->

<!-- START_757017b119bfbf439eaae04787af04aa -->
## api/v1/admin/grow/harvests/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/harvests/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/harvests/{id}`


<!-- END_757017b119bfbf439eaae04787af04aa -->

<!-- START_d38d4c2599903925416fc05383d1efe8 -->
## api/v1/admin/grow/items
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/items" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/items`


<!-- END_d38d4c2599903925416fc05383d1efe8 -->

<!-- START_94686d729f294e746f26a8ce547726be -->
## api/v1/admin/grow/items/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/items/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/items/export/{type}`


<!-- END_94686d729f294e746f26a8ce547726be -->

<!-- START_9952c04a138610a7bf857c3217a7141c -->
## api/v1/admin/grow/items/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/items/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/items/{id}/export/{type}`


<!-- END_9952c04a138610a7bf857c3217a7141c -->

<!-- START_4ae9c20ec778fefd2209ec4d36725a86 -->
## api/v1/admin/grow/items/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/items/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/items/{id}`


<!-- END_4ae9c20ec778fefd2209ec4d36725a86 -->

<!-- START_3bf20aad9d39d34ced2898c2d231ffc3 -->
## api/v1/admin/grow/items
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/grow/items" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/grow/items`


<!-- END_3bf20aad9d39d34ced2898c2d231ffc3 -->

<!-- START_a1d28d9562e07dd4adb8ba4b0edc2c89 -->
## api/v1/admin/grow/items/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/grow/items/{id}`


<!-- END_a1d28d9562e07dd4adb8ba4b0edc2c89 -->

<!-- START_576d4ff5b6ba243052ae63cd32db4210 -->
## api/v1/admin/grow/items/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/items/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/grow/items/{id}`


<!-- END_576d4ff5b6ba243052ae63cd32db4210 -->

<!-- START_640cb81c2729d91cf6fac2a1609f401e -->
## api/v1/admin/grow/packages
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/packages" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/packages"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/packages`


<!-- END_640cb81c2729d91cf6fac2a1609f401e -->

<!-- START_b1070b284331ee3acc9ee2cbc656abc0 -->
## api/v1/admin/grow/packages/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/packages/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/packages/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/packages/batch`


<!-- END_b1070b284331ee3acc9ee2cbc656abc0 -->

<!-- START_135444c3cbd1b4dfa4b398563cdf39ed -->
## api/v1/admin/grow/packages/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/packages/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/packages/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/packages/{id}/export/{type}`


<!-- END_135444c3cbd1b4dfa4b398563cdf39ed -->

<!-- START_ec7580d924e7ae380e53d8e610e853c9 -->
## api/v1/admin/grow/packages/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/packages/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/packages/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/packages/{id}`


<!-- END_ec7580d924e7ae380e53d8e610e853c9 -->

<!-- START_0f14b08e8224cd92a140dfe866305c24 -->
## api/v1/admin/grow/plants
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plants" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plants"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plants`


<!-- END_0f14b08e8224cd92a140dfe866305c24 -->

<!-- START_45e6b0e500f3945bd550aeef22760ef5 -->
## api/v1/admin/grow/plants/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plants/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plants/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plants/batch`


<!-- END_45e6b0e500f3945bd550aeef22760ef5 -->

<!-- START_8034c951499573a5ad4a8871eb29449c -->
## api/v1/admin/grow/plants/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plants/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plants/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plants/export/{type}`


<!-- END_8034c951499573a5ad4a8871eb29449c -->

<!-- START_e4c1ac6f133a8c196786fad4d8a764be -->
## api/v1/admin/grow/plants/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plants/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plants/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plants/{id}/export/{type}`


<!-- END_e4c1ac6f133a8c196786fad4d8a764be -->

<!-- START_5b5cca6c9b0298ef5d1e4168e6123c8c -->
## api/v1/admin/grow/plants/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plants/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plants/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plants/{id}`


<!-- END_5b5cca6c9b0298ef5d1e4168e6123c8c -->

<!-- START_ea641734236d970f5ae3a840a10c71da -->
## api/v1/admin/grow/plantbatches
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plantbatches`


<!-- END_ea641734236d970f5ae3a840a10c71da -->

<!-- START_7aa4e8892a9d400e7bc0eeadf50ebcd2 -->
## api/v1/admin/grow/plantbatches/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plantbatches/export/{type}`


<!-- END_7aa4e8892a9d400e7bc0eeadf50ebcd2 -->

<!-- START_fb8701c99b3ffe21d828d6058d2626e1 -->
## api/v1/admin/grow/plantbatches/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plantbatches/{id}/export/{type}`


<!-- END_fb8701c99b3ffe21d828d6058d2626e1 -->

<!-- START_09608b6ac8d7bd2aab308f0f84381d26 -->
## api/v1/admin/grow/plantbatches/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/plantbatches/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/plantbatches/{id}`


<!-- END_09608b6ac8d7bd2aab308f0f84381d26 -->

<!-- START_704fd501f5d9e2334ec7c8e540727f06 -->
## api/v1/admin/grow/rooms
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/rooms" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/rooms`


<!-- END_704fd501f5d9e2334ec7c8e540727f06 -->

<!-- START_c2ec4d7a79efd552744bb1a9a93689f7 -->
## api/v1/admin/grow/rooms/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/rooms/export/{type}`


<!-- END_c2ec4d7a79efd552744bb1a9a93689f7 -->

<!-- START_7cb6e654b6e4aad0a907c6bc76ac867b -->
## api/v1/admin/grow/rooms/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/rooms/{id}/export/{type}`


<!-- END_7cb6e654b6e4aad0a907c6bc76ac867b -->

<!-- START_84938f5c039c8b33e700c23d1296e9dc -->
## api/v1/admin/grow/rooms/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/rooms/{id}`


<!-- END_84938f5c039c8b33e700c23d1296e9dc -->

<!-- START_eb9aa901e267919ac8377e3cb7aa1863 -->
## api/v1/admin/grow/rooms
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/grow/rooms`


<!-- END_eb9aa901e267919ac8377e3cb7aa1863 -->

<!-- START_595c14b036888372631b79d9d3a647d2 -->
## api/v1/admin/grow/rooms/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/grow/rooms/{id}`


<!-- END_595c14b036888372631b79d9d3a647d2 -->

<!-- START_8da79304eb7d77c504b28bd6ccd22ea8 -->
## api/v1/admin/grow/rooms/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/rooms/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/grow/rooms/{id}`


<!-- END_8da79304eb7d77c504b28bd6ccd22ea8 -->

<!-- START_c0de62c75b922a4731b3890d314ec43b -->
## api/v1/admin/grow/strains
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/strains" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/strains`


<!-- END_c0de62c75b922a4731b3890d314ec43b -->

<!-- START_28c48c3ab6d12a63b3630be329d04e0a -->
## api/v1/admin/grow/strains/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/strains/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/strains/export/{type}`


<!-- END_28c48c3ab6d12a63b3630be329d04e0a -->

<!-- START_2f002ec91776908f0daaa88966bd141d -->
## api/v1/admin/grow/strains/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/strains/{id}/export/{type}`


<!-- END_2f002ec91776908f0daaa88966bd141d -->

<!-- START_86cac6ea95698bbe3b513de0ad03fe66 -->
## api/v1/admin/grow/strains/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/grow/strains/{id}`


<!-- END_86cac6ea95698bbe3b513de0ad03fe66 -->

<!-- START_ceac726a1c53701e682d8b2046663dcd -->
## api/v1/admin/grow/strains
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/grow/strains`


<!-- END_ceac726a1c53701e682d8b2046663dcd -->

<!-- START_6817d3b2a37aa847cbfc2735599917a7 -->
## api/v1/admin/grow/strains/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/grow/strains/{id}`


<!-- END_6817d3b2a37aa847cbfc2735599917a7 -->

<!-- START_77e556bddf521d0f805a619373aeb7a6 -->
## api/v1/admin/grow/strains/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/grow/strains/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/grow/strains/{id}`


<!-- END_77e556bddf521d0f805a619373aeb7a6 -->

<!-- START_2be1f0e022faf424f18f30275e61416e -->
## api/v1/auth/login
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/login"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/login`


<!-- END_2be1f0e022faf424f18f30275e61416e -->

<!-- START_a68ff660ea3d08198e527df659b17963 -->
## api/v1/auth/logout
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/logout" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/logout"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/logout`


<!-- END_a68ff660ea3d08198e527df659b17963 -->

<!-- START_3157fb6d77831463001829403e201c3e -->
## api/v1/auth/register
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/register" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/register"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/register`


<!-- END_3157fb6d77831463001829403e201c3e -->

<!-- START_264241560a9352edceec33fc6afa99c6 -->
## api/v1/auth/activate/{token}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/auth/activate/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/activate/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/auth/activate/{token}`


<!-- END_264241560a9352edceec33fc6afa99c6 -->

<!-- START_666ad190c1923c40a0d224c7bf0b686a -->
## api/v1/auth/password
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/password" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/password"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/password`


<!-- END_666ad190c1923c40a0d224c7bf0b686a -->

<!-- START_79bff0573f796fcf22501366aebf3cdc -->
## api/v1/auth/reactivateRequest
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/reactivateRequest" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/reactivateRequest"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/reactivateRequest`


<!-- END_79bff0573f796fcf22501366aebf3cdc -->

<!-- START_4dba4a4a91c4ddcbf7df1f60d2f06560 -->
## api/v1/auth/validate-password-reset
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/validate-password-reset" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/validate-password-reset"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/validate-password-reset`


<!-- END_4dba4a4a91c4ddcbf7df1f60d2f06560 -->

<!-- START_78aa97c8d55bdb83e012f09fe046a2c6 -->
## api/v1/auth/reset
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/auth/reset" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/reset"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/auth/reset`


<!-- END_78aa97c8d55bdb83e012f09fe046a2c6 -->

<!-- START_a2c5faeaa0de79c0835fe6b500ace2f6 -->
## api/v1/auth/clearLocation/{location}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/auth/clearLocation/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/auth/clearLocation/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/auth/clearLocation/{location}`


<!-- END_a2c5faeaa0de79c0835fe6b500ace2f6 -->

<!-- START_f7aec74f77535d3c43d7b846478a9299 -->
## api/v1/admin/auth/locations/load
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/locations/load" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/load"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/locations/load`


<!-- END_f7aec74f77535d3c43d7b846478a9299 -->

<!-- START_75323f1a01db660376bda3c4aaa6dabd -->
## api/v1/admin/auth/locations/change/{location}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/locations/change/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/change/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/locations/change/{location}`


<!-- END_75323f1a01db660376bda3c4aaa6dabd -->

<!-- START_a72eb9ee16f32bcd4dbd71f51a383e86 -->
## api/v1/admin/auth/location/ismedical/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/location/ismedical/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/location/ismedical/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/location/ismedical/{id}`


<!-- END_a72eb9ee16f32bcd4dbd71f51a383e86 -->

<!-- START_037379de966faf8a833b350b99c9606a -->
## api/v1/admin/auth/location/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/location/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/location/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/location/{id}`


<!-- END_037379de966faf8a833b350b99c9606a -->

<!-- START_d70c5835e12520261e47804ad5870856 -->
## api/v1/admin/auth/location/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/auth/location/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/location/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/auth/location/{id}`


<!-- END_d70c5835e12520261e47804ad5870856 -->

<!-- START_ada019e3e894df558132f3c4789c4a1c -->
## api/v1/admin/auth/users
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/users" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/users`


<!-- END_ada019e3e894df558132f3c4789c4a1c -->

<!-- START_9c45752da86fabc77879dc7881768021 -->
## api/v1/admin/auth/users/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/users/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/users/export/{type}`


<!-- END_9c45752da86fabc77879dc7881768021 -->

<!-- START_a15460798c51377123712e2a8827f3f3 -->
## api/v1/admin/auth/users/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/users/{id}/export/{type}`


<!-- END_a15460798c51377123712e2a8827f3f3 -->

<!-- START_a7b1d70267b28de05e963a300b7b4545 -->
## api/v1/admin/auth/users/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/users/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/users/{id}`


<!-- END_a7b1d70267b28de05e963a300b7b4545 -->

<!-- START_e130a4a2460dae77f1140923b429e6d2 -->
## api/v1/admin/auth/users/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/auth/users/{id}`


<!-- END_e130a4a2460dae77f1140923b429e6d2 -->

<!-- START_8a8190ecece431389240365097a79d1c -->
## api/v1/admin/auth/users/{id}/change-password
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/change-password" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/change-password"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users/{id}/change-password`


<!-- END_8a8190ecece431389240365097a79d1c -->

<!-- START_4796cae6fe0e5a784533c1f23615b15c -->
## api/v1/admin/auth/users/{id}/change-pin
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/change-pin" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/change-pin"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users/{id}/change-pin`


<!-- END_4796cae6fe0e5a784533c1f23615b15c -->

<!-- START_79ff6c1448414ba15eba02e1b6a9ffda -->
## api/v1/admin/auth/users/{id}/permissions
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/permissions" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/permissions"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users/{id}/permissions`


<!-- END_79ff6c1448414ba15eba02e1b6a9ffda -->

<!-- START_e8c26ddc3594f6035fc1ea44da0b1b9e -->
## api/v1/admin/auth/users/{id}/send-activation
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/send-activation" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1/send-activation"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users/{id}/send-activation`


<!-- END_e8c26ddc3594f6035fc1ea44da0b1b9e -->

<!-- START_2ad69793ee157fef1648e29f3ab74806 -->
## api/v1/admin/auth/users/{id}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users/{id}`


<!-- END_2ad69793ee157fef1648e29f3ab74806 -->

<!-- START_d204a1199f403915771eedae74b30eda -->
## api/v1/admin/auth/users
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/users`


<!-- END_d204a1199f403915771eedae74b30eda -->

<!-- START_1ca521bc317a906930df9ab65d2a124a -->
## api/v1/admin/auth/users/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/users/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/auth/users/{id}`


<!-- END_1ca521bc317a906930df9ab65d2a124a -->

<!-- START_b8adb275f96c47fa3a7ebcb3e3f0e5d1 -->
## api/v1/admin/auth/addressbook
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/addressbook`


<!-- END_b8adb275f96c47fa3a7ebcb3e3f0e5d1 -->

<!-- START_c044444e6e6bcfd7547aff775511e9de -->
## api/v1/admin/auth/addressbook/batch
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/batch" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/batch"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/addressbook/batch`


<!-- END_c044444e6e6bcfd7547aff775511e9de -->

<!-- START_7d0363870fd9924fc9d02eafeda1c49f -->
## api/v1/admin/auth/addressbook/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/addressbook/export/{type}`


<!-- END_7d0363870fd9924fc9d02eafeda1c49f -->

<!-- START_a56627b805af19d77e70de8dc2f7f1ff -->
## api/v1/admin/auth/addressbook/{id}/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/addressbook/{id}/export/{type}`


<!-- END_a56627b805af19d77e70de8dc2f7f1ff -->

<!-- START_4c8bf4ae7fe0edad0f5a2d335f0839f7 -->
## api/v1/admin/auth/addressbook/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/addressbook/{id}`


<!-- END_4c8bf4ae7fe0edad0f5a2d335f0839f7 -->

<!-- START_e50b800e1d11b25ee1529917a18f52e4 -->
## api/v1/admin/auth/addressbook/batch/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/batch/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/batch/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/addressbook/batch/{type}`


<!-- END_e50b800e1d11b25ee1529917a18f52e4 -->

<!-- START_94f2b5c6b2d621c09da30418b0b11927 -->
## api/v1/admin/auth/addressbook
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/addressbook`


<!-- END_94f2b5c6b2d621c09da30418b0b11927 -->

<!-- START_0d5fbfb95ed6dc347be3a59caaa82c7d -->
## api/v1/admin/auth/addressbook/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/auth/addressbook/{id}`


<!-- END_0d5fbfb95ed6dc347be3a59caaa82c7d -->

<!-- START_fce33f5ebe1528f4ee1230ee4c4a3e95 -->
## api/v1/admin/auth/addressbook/{id}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/addressbook/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/auth/addressbook/{id}`


<!-- END_fce33f5ebe1528f4ee1230ee4c4a3e95 -->

<!-- START_fe4bb18e990a5146940a21913f05d441 -->
## api/v1/admin/auth/toggleMode
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/toggleMode" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/toggleMode"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/toggleMode`


<!-- END_fe4bb18e990a5146940a21913f05d441 -->

<!-- START_0e5e8c7dced8c58d4b09a6ced929fd47 -->
## api/v1/admin/auth/registerLocation
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/registerLocation" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/registerLocation"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/registerLocation`


<!-- END_0e5e8c7dced8c58d4b09a6ced929fd47 -->

<!-- START_ccc3052defb052be5edb1a170d11bc89 -->
## api/v1/admin/auth/newToken
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/newToken" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/newToken"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/newToken`


<!-- END_ccc3052defb052be5edb1a170d11bc89 -->

<!-- START_a9170271b5b3613d87eb71a42a40006c -->
## api/v1/admin/auth/testMigrationConnection/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/testMigrationConnection/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/testMigrationConnection/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/testMigrationConnection/{type}`


<!-- END_a9170271b5b3613d87eb71a42a40006c -->

<!-- START_effd4cd6ec54b6ce5537be2e39102e7e -->
## api/v1/admin/auth/startMigration/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/startMigration/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/startMigration/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/startMigration/{type}`


<!-- END_effd4cd6ec54b6ce5537be2e39102e7e -->

<!-- START_3415ab04bf77610f1e16882401bbaca9 -->
## api/v1/admin/auth/locations
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/locations" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/locations`


<!-- END_3415ab04bf77610f1e16882401bbaca9 -->

<!-- START_cae54874e608069f3a35efd600b7c557 -->
## api/v1/admin/auth/locations/export/{type}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/locations/export/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/export/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/locations/export/{type}`


<!-- END_cae54874e608069f3a35efd600b7c557 -->

<!-- START_5d7012506a84b468b36b5fb801d28eaa -->
## api/v1/admin/auth/locations/{id}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/locations/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/locations/{id}`


<!-- END_5d7012506a84b468b36b5fb801d28eaa -->

<!-- START_0ef4f79a04ce13c5edc6c5db64735a48 -->
## api/v1/admin/auth/locations/{id}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/locations/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/auth/locations/{id}`


<!-- END_0ef4f79a04ce13c5edc6c5db64735a48 -->

<!-- START_aca55b4db3c3112ee9cfec2eaae2574a -->
## api/v1/admin/auth/modify/{id}/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/modify/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/modify/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/modify/{id}/{type}`


<!-- END_aca55b4db3c3112ee9cfec2eaae2574a -->

<!-- START_f935097471978fdccdc26d462c55bfab -->
## api/v1/admin/auth/schemasform
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/auth/schemasform`


<!-- END_f935097471978fdccdc26d462c55bfab -->

<!-- START_430689580db99b9f3e7521e4cd54ea86 -->
## api/v1/admin/auth/schemasform/{code}
> Example request:

```bash
curl -X PUT \
    "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/v1/admin/auth/schemasform/{code}`


<!-- END_430689580db99b9f3e7521e4cd54ea86 -->

<!-- START_3359574f0c89db89fac85ac70ee100bd -->
## api/v1/admin/auth/schemasform/{code}
> Example request:

```bash
curl -X DELETE \
    "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/schemasform/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/admin/auth/schemasform/{code}`


<!-- END_3359574f0c89db89fac85ac70ee100bd -->

<!-- START_856b5a17c46859a49bd2b311f6b2d6ff -->
## api/v1/admin/auth/pin
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/auth/pin" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/auth/pin"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/auth/pin`


<!-- END_856b5a17c46859a49bd2b311f6b2d6ff -->

<!-- START_8e1c824432adbf5864c4f5df68a09619 -->
## api/v1/admin/schemas/{module}/{list}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/schemas/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/schemas/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/schemas/{module}/{list}`


<!-- END_8e1c824432adbf5864c4f5df68a09619 -->

<!-- START_f43a257460823a20c35f261cbff8e6b6 -->
## api/v1/admin/regions/{country}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/regions/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/regions/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/regions/{country}`


<!-- END_f43a257460823a20c35f261cbff8e6b6 -->

<!-- START_c66495896de69394f3858f1e3173f300 -->
## api/v1/admin/asset/{model}/{type}
> Example request:

```bash
curl -X POST \
    "http://devcloud.hotbox.today/api/v1/admin/asset/1/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/asset/1/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/admin/asset/{model}/{type}`


<!-- END_c66495896de69394f3858f1e3173f300 -->

<!-- START_4b97416166b4ab8a9b598288e0957cce -->
## api/v1/admin/graph/{name}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/api/v1/admin/graph/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/api/v1/admin/graph/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (429):

```json
{
    "message": "Too Many Attempts."
}
```

### HTTP Request
`GET api/v1/admin/graph/{name}`


<!-- END_4b97416166b4ab8a9b598288e0957cce -->

<!-- START_1cf43baf26807f88088b3a04a00617d3 -->
## auth/social/{provider}
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/auth/social/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/auth/social/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (302):

```json
null
```

### HTTP Request
`GET auth/social/{provider}`


<!-- END_1cf43baf26807f88088b3a04a00617d3 -->

<!-- START_2548569efb59429767fc28e251ad4c25 -->
## auth/{provider}/callback
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/auth/1/callback" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/auth/1/callback"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`GET auth/{provider}/callback`


<!-- END_2548569efb59429767fc28e251ad4c25 -->

<!-- START_d902a01410694a746fa69de422d5d77b -->
## hotboxiconlibrary
> Example request:

```bash
curl -X GET \
    -G "http://devcloud.hotbox.today/hotboxiconlibrary" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://devcloud.hotbox.today/hotboxiconlibrary"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET hotboxiconlibrary`


<!-- END_d902a01410694a746fa69de422d5d77b -->


