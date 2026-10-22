#!/usr/bin/env python3
"""Exercise the real PHP endpoints with maintenance on, before any DB access."""
import json, os, pathlib, shutil, socket, subprocess, tempfile, time, urllib.request, urllib.error

source = pathlib.Path(__file__).resolve().parents[1] / "hasa-api"
with tempfile.TemporaryDirectory(prefix="hasa-maintenance-") as folder:
    root = pathlib.Path(folder) / "api"
    shutil.copytree(source, root, ignore=shutil.ignore_patterns("config.php", "*.log", "maintenance.flag"))
    # Invalid private configuration must never execute during maintenance.
    (root / "config.php").write_text("<?php throw new RuntimeException('CONFIG_MUST_NOT_RUN');", encoding="utf-8")
    (root / "maintenance.flag").touch()
    with socket.socket() as sock:
        sock.bind(("127.0.0.1", 0))
        port = sock.getsockname()[1]
    env = os.environ.copy()
    process = subprocess.Popen(["php", "-S", f"127.0.0.1:{port}", "-t", str(root)],
                               stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, env=env)
    def request(path, method="GET"):
        req = urllib.request.Request(f"http://127.0.0.1:{port}/"+path,
                                     data=b"{}" if method=="POST" else None, method=method)
        try:
            response = urllib.request.urlopen(req, timeout=5)
        except urllib.error.HTTPError as error:
            response = error
        with response:
            return response.status, response.headers, response.read()
    try:
        for attempt in range(100):
            try:
                request("health.php")
                break
            except urllib.error.URLError:
                time.sleep(.05)
        else:
            raise AssertionError("PHP test server did not start")
        endpoints=["health.php","index.php","systems.php","prospection-reports.php",
                   "event-record.php","production-samples.php","auth-status.php",
                   "login.php","logout.php","password-change.php","settings.php",
                   "user-admin.php","sharing-install.php","events-install.php",
                   "galaxy.php","galaxy-read.php","galaxies.php","events.php",
                   "filter-options.php","prospection-read.php"]
        count=0
        for endpoint in endpoints:
            for method in ("GET","POST"):
                status,headers,body=request(endpoint,method)
                assert status==503,(endpoint,method,status,body[:100])
                assert headers["Retry-After"]=="300",(endpoint,method)
                result=json.loads(body)
                assert result["ok"] is False and result["error"]=="maintenance",(endpoint,method,result)
                assert b"CONFIG_MUST_NOT_RUN" not in body
                count+=1
        for method in ("GET","POST"):
            status,headers,body=request("tools/user-admin.php",method)
            assert status==404 and b"CONFIG_MUST_NOT_RUN" not in body
            count+=1
        status,headers,body=request("health.php","HEAD")
        assert status==503 and not body
        count+=1
        (root / "config.php").unlink()
        (root / "maintenance.flag").unlink()
        status,headers,body=request("health.php")
        assert status==503 and json.loads(body)["error"]=="configuration_missing"
        count+=1
        print(f"{count} real HTTP maintenance checks passed: every endpoint blocked before config/DB, HEAD and recovery")
    finally:
        process.terminate()
        process.wait(timeout=5)
