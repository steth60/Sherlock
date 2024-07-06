import React, { useState, useEffect } from 'react';

const Tabs = ({ instance }) => {
    const [activeTab, setActiveTab] = useState('control');
    const [consoleOutput, setConsoleOutput] = useState('');

    useEffect(() => {
        if (activeTab === 'console') {
            fetchConsoleOutput();
            const interval = setInterval(fetchConsoleOutput, 5000);
            return () => clearInterval(interval);
        }
    }, [activeTab]);

    const fetchConsoleOutput = async () => {
        const response = await fetch(`/instances/${instance.id}/output`);
        const data = await response.json();
        setConsoleOutput(data.output);
    };

    return (
        <div>
            <ul className="nav nav-tabs" id="myTab" role="tablist">
                <li className="nav-item">
                    <button className={`nav-link ${activeTab === 'control' ? 'active' : ''}`} onClick={() => setActiveTab('control')}>Control</button>
                </li>
                <li className="nav-item">
                    <button className={`nav-link ${activeTab === 'console' ? 'active' : ''}`} onClick={() => setActiveTab('console')}>Console</button>
                </li>
                <li className="nav-item">
                    <button className={`nav-link ${activeTab === 'scheduling' ? 'active' : ''}`} onClick={() => setActiveTab('scheduling')}>Scheduling</button>
                </li>
                <li className="nav-item">
                    <button className={`nav-link ${activeTab === 'env-variables' ? 'active' : ''}`} onClick={() => setActiveTab('env-variables')}>Environment Variables</button>
                </li>
            </ul>

            <div className="tab-content" id="myTabContent">
                <div className={`tab-pane fade ${activeTab === 'control' ? 'show active' : ''}`} id="control" role="tabpanel">
                    <div className="mt-3">
                        <form action={`/instances/${instance.id}/start`} method="POST" style={{ display: 'inline' }}>
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]').getAttribute('content')} />
                            <button type="submit" className="btn btn-success">Start</button>
                        </form>
                        <form action={`/instances/${instance.id}/stop`} method="POST" style={{ display: 'inline' }}>
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]').getAttribute('content')} />
                            <button type="submit" className="btn btn-warning">Stop</button>
                        </form>
                        <form action={`/instances/${instance.id}/restart`} method="POST" style={{ display: 'inline' }}>
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]').getAttribute('content')} />
                            <button type="submit" className="btn btn-info">Restart</button>
                        </form>
                        <form action={`/instances/${instance.id}/delete`} method="POST" style={{ display: 'inline' }}>
                            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]').getAttribute('content')} />
                            <button type="submit" className="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
                <div className={`tab-pane fade ${activeTab === 'console' ? 'show active' : ''}`} id="console" role="tabpanel">
                    <div className="mt-3">
                        <pre id="console-output" className="bg-dark text-white p-3" style={{ height: '400px', overflowY: 'scroll' }}>{consoleOutput}</pre>
                    </div>
                </div>
                <div className={`tab-pane fade ${activeTab === 'scheduling' ? 'show active' : ''}`} id="scheduling" role="tabpanel">
                    <div className="mt-3">
                        {/* Scheduling content will go here */}
                    </div>
                </div>
                <div className={`tab-pane fade ${activeTab === 'env-variables' ? 'show active' : ''}`} id="env-variables" role="tabpanel">
                    <div className="mt-3">
                        {/* Environment variables content will go here */}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Tabs;
