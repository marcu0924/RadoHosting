<style>
  :root{
    --bg: #0b0f17;
    --card: #111827;
    --border: rgba(255,255,255,.08);
    --text: #e5e7eb;
    --muted: #9ca3af;
    --accent: #34d399; /* emerald-ish */
  }

  body{
    margin: 0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    background: radial-gradient(1200px 600px at 20% -10%, rgba(52,211,153,.18), transparent 55%),
                radial-gradient(900px 500px at 80% 0%, rgba(59,130,246,.12), transparent 50%),
                var(--bg);
    color: var(--text);
    line-height: 1.5;
    padding: 48px 18px;
  }

  .tasks{
    max-width: 720px;
    margin: 0 auto;
    background: rgba(17,24,39,.85);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(0,0,0,.45);
    overflow: hidden;
  }

  .tasks__header{
    padding: 20px 22px 14px;
    border-bottom: 1px solid var(--border);
  }

  .tasks__header h1{
    margin: 0;
    font-size: 26px;
    letter-spacing: .2px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .badge{
    font-size: 12px;
    color: var(--muted);
    border: 1px solid var(--border);
    padding: 4px 10px;
    border-radius: 999px;
  }

  .tasks__body{
    padding: 10px 10px 14px;
  }

  ul.task-list{
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 10px;
  }

  .task{
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 14px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
    transition: transform .15s ease, border-color .15s ease, background .15s ease;
  }

  .task:hover{
    transform: translateY(-1px);
    border-color: rgba(52,211,153,.35);
    background: linear-gradient(180deg, rgba(52,211,153,.08), rgba(255,255,255,.01));
  }

  .task input[type="checkbox"]{
    width: 18px;
    height: 18px;
    margin-top: 2px;
    accent-color: var(--accent);
    flex: 0 0 auto;
  }

  .task label{
    cursor: pointer;
    user-select: none;
    color: var(--text);
  }

  .task small{
    display: block;
    margin-top: 4px;
    color: var(--muted);
    font-size: 12.5px;
  }

  /* Completed state */
  .task input:checked + label{
    color: rgba(229,231,235,.65);
    text-decoration: line-through;
  }

  .footer-note{
    padding: 14px 22px 18px;
    color: var(--muted);
    font-size: 13px;
    border-top: 1px solid var(--border);
  }
</style>

<div class="tasks">
  <div class="tasks__header">
    <h1>
      Tasks
      <span class="badge">Rado Hosting • Roadmap</span>
    </h1>
  </div>

  <div class="tasks__body">
    <ul class="task-list">
      <li class="task">
        <input id="t1" type="checkbox">
        <label for="t1">
          Add users capability to make an order for a server.
          <small>Collect server options → create order → provision container.</small>
        </label>
      </li>

      <li class="task">
        <input id="t2" type="checkbox">
        <label for="t2">
          List the users servers in a dashboard of some sort.
          <small>Status, ports, uptime, actions (restart/stop), quick links.</small>
        </label>
      </li>

      <li class="task">
        <input id="t3" type="checkbox">
        <label for="t3">
          Add some kind of payment (Example Stripe + Laravel Cashier).
          <small>Subscription tiers + webhook handling + plan gating.</small>
        </label>
      </li>

      <li class="task">
        <input id="t4" type="checkbox">
        <label for="t4">
          What else do we need?
          <small>Think: roles, limits, billing portal, support tickets, logs.</small>
        </label>
      </li>
    </ul>
  </div>

  <div class="footer-note">
    Tip: check items off as you ship features. Keep this page as your “current sprint”.
  </div>
</div>