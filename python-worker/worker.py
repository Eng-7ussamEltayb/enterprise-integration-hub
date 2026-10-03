#!/usr/bin/env python3
"""Worker بسيط يعالج المهام من ملف الطابور (ملف مشترك للعرض)."""
from __future__ import annotations

import json
import time
from pathlib import Path

QUEUE = Path(__file__).resolve().parents[1] / "php-hub" / "storage" / "queue.json"


def process_once() -> int:
    if not QUEUE.exists():
        print("Queue file missing")
        return 0
    jobs = json.loads(QUEUE.read_text(encoding="utf-8") or "[]")
    done = 0
    for job in jobs:
        if job.get("status") == "queued":
            job["status"] = "processed"
            job["processed_at"] = time.strftime("%Y-%m-%dT%H:%M:%S")
            done += 1
            print(f"Processed job #{job['id']} type={job.get('type')}")
    QUEUE.write_text(json.dumps(jobs, ensure_ascii=False, indent=2), encoding="utf-8")
    return done


if __name__ == "__main__":
    n = process_once()
    print(f"Done. processed={n}")
