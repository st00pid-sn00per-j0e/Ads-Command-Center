import React from 'react';
import { Inertia } from '@inertiajs/inertia';

export default function Index({ specialists, organization, flash }) {
  const handleToggle = (id, status) => {
    Inertia.post(route('admin.specialists.updateStatus', id), { status }, { preserveState: false });
  };

  return (
    <div>
      <h1>Specialists — {organization?.name}</h1>
      {flash?.success && <div className="alert alert-success">{flash.success}</div>}
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {specialists.map((s) => (
            <tr key={s.id}>
              <td>{s.name}</td>
              <td>{s.email}</td>
              <td>{s.status}</td>
              <td>
                {s.status === 'active' ? (
                  <button onClick={() => handleToggle(s.id, 'inactive')}>Disable</button>
                ) : (
                  <button onClick={() => handleToggle(s.id, 'active')}>Enable</button>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
