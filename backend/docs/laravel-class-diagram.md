# Laravel application — class diagram

This document describes the **current** `App\` layer and how it sits in the broader ingest architecture. The flowchart matches the HTTPS-only option from the project README; the class diagram reflects code under [`app/`](../app/) today.

## System context (flowchart)

Clients and AWS-shaped components (your design target). Laravel today runs the **ingest + persistence** role directly; later an external **Ingestor** can enqueue to SQS before workers hit Laravel.

```mermaid
flowchart TB
  subgraph Clients["Clients"]
    App["Node app\n(POST log batches)"]
    UI["Admin UI\n(GET + poll / refresh)"]
  end

  subgraph OpenAIREST["OpenAI — HTTPS only\n(unless optional C)"]
    Chat["Chat Completions /\nembeddings REST"]
  end

  subgraph AWS["AWS — REST at edge only"]
    GW["API Gateway\nHTTP API only"]
    Ing["Ingestor\nLambda or container\nPOST → validate → SQS"]
    Q["SQS"]
    Lar["Laravel\nworkers + dashboard API"]
    Data["RDS + S3"]
  end

  App -->|"HTTPS POST\ningest_https"| GW
  UI -->|"HTTPS GET\ndashboard / cursor"| GW
  GW --> Ing
  Ing --> Q
  Q --> Lar
  Lar --> Data
  GW --> Lar
  App -->|"HTTPS"| Chat
```

## Laravel `App` class diagram (current codebase)

Relationships: **`IngestController`** validates HTTP input and persists **`ObservabilityEvent`** rows. **`User`** is the default Laravel auth model (sessions / future admin UI); it is not yet linked to observability events.

```mermaid
classDiagram
  direction TB

  class Controller {
    <<abstract>>
  }

  class IngestController {
    +store(request: Request) JsonResponse
  }

  class User {
    +name: string
    +email: string
    +password: string
    casts()
  }

  class ObservabilityEvent {
    +schema_version: string
    +event_id: uuid
    +event_type: string
    +severity: string
    +occurred_at: datetime
    +project_id: string
    +user_id: string?
    +service_type: string?
    +request_id: string?
    +tokens_used: int?
    +latency_ms: int?
    +has_correction: bool
    +has_recommended: bool
    +has_appointment: bool
    +provider: string?
    +model: string?
    +payload: array?
    casts()
  }

  class Model {
    <<Illuminate Eloquent>>
  }

  class Authenticatable {
    <<Illuminate Foundation>>
  }

  class Request {
    <<Illuminate Http>>
  }

  class JsonResponse {
    <<Illuminate Http>>
  }

  Controller <|-- IngestController
  Model <|-- ObservabilityEvent
  Authenticatable <|-- User

  IngestController ..> Request : validates
  IngestController ..> JsonResponse : returns
  IngestController ..> ObservabilityEvent : create()
```

## Future classes (planned, not implemented)

When you add projects, API keys, and a dashboard API, expect something like:

- **`Project`** — `hasMany` **`ObservabilityEvent`**, **`ApiToken`**
- **`ApiToken`** — authenticates ingest requests (middleware)
- **`DashboardController`** or **`EventController`** — `index` / cursor reads for admin UI
- **`ProcessIngestJob`** — if ingest moves to SQS: worker receives payload and creates **`ObservabilityEvent`**

You can extend this file’s Mermaid block as those classes land.
