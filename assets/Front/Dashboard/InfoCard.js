import React from "react";

export default class InfoCard extends React.Component {
    render() {
        return (
            <div className="card card-info card-body">
                <p><span className="text-muted small">Today's code:</span><br/>
                    <span className="strong">xyz46</span>
                </p>
                <p><span className="text-muted small">Today's transactions</span><br/>
                    <span className="strong">547</span></p>
                <p><span className="text-muted small">Active transactions:</span><br/>
                    <span className="strong">3</span></p>
            </div>
        )
    }
}