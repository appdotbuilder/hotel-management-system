import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Reservation {
    id: number;
    confirmation_number: string;
    guest: {
        first_name: string;
        last_name: string;
        email: string;
    };
    room: {
        room_number: string;
        type: string;
    };
    check_in_date: string;
    check_out_date: string;
    adults: number;
    children: number;
    total_amount: number;
    status: string;
    nights: number;
}

interface Props {
    reservations: {
        data: Reservation[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        meta: {
            total: number;
            per_page: number;
            current_page: number;
        };
    };
    [key: string]: unknown;
}

const StatusBadge = ({ status }: { status: string }) => {
    const getStatusColor = (status: string) => {
        switch (status) {
            case 'confirmed': return 'bg-blue-500';
            case 'checked_in': return 'bg-green-500';
            case 'checked_out': return 'bg-gray-500';
            case 'cancelled': return 'bg-red-500';
            case 'no_show': return 'bg-orange-500';
            default: return 'bg-gray-500';
        }
    };

    return (
        <Badge className={`${getStatusColor(status)} text-white`}>
            {status.replace('_', ' ')}
        </Badge>
    );
};

export default function ReservationsIndex({ reservations }: Props) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    };

    return (
        <AppShell>
            <Head title="Reservations" />
            
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">📋 Reservations</h1>
                        <p className="text-gray-600 mt-2">Manage hotel bookings and guest check-ins</p>
                    </div>
                    <Link href={route('reservations.create')}>
                        <Button>+ New Reservation</Button>
                    </Link>
                </div>

                {/* Reservations List */}
                <div className="space-y-4">
                    {reservations.data.map((reservation) => (
                        <Card key={reservation.id} className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle className="flex items-center space-x-2">
                                            <span>{reservation.guest.first_name} {reservation.guest.last_name}</span>
                                            <StatusBadge status={reservation.status} />
                                        </CardTitle>
                                        <p className="text-gray-600 text-sm mt-1">
                                            {reservation.confirmation_number} • {reservation.guest.email}
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-semibold text-lg text-green-600">
                                            ${reservation.total_amount}
                                        </p>
                                    </div>
                                </div>
                            </CardHeader>
                            
                            <CardContent>
                                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <p className="text-sm text-gray-600">Room</p>
                                        <p className="font-medium">
                                            {reservation.room.room_number} ({reservation.room.type})
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-600">Check-in</p>
                                        <p className="font-medium">{formatDate(reservation.check_in_date)}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-600">Check-out</p>
                                        <p className="font-medium">{formatDate(reservation.check_out_date)}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-600">Guests</p>
                                        <p className="font-medium">
                                            {reservation.adults} adults
                                            {reservation.children > 0 && `, ${reservation.children} children`}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex space-x-2">
                                    <Link href={route('reservations.show', reservation.id)}>
                                        <Button variant="outline" size="sm">View Details</Button>
                                    </Link>
                                    <Link href={route('reservations.edit', reservation.id)}>
                                        <Button size="sm">Edit</Button>
                                    </Link>
                                    {reservation.status === 'confirmed' && (
                                        <Button variant="outline" size="sm" className="text-green-600 border-green-600">
                                            Check In
                                        </Button>
                                    )}
                                    {reservation.status === 'checked_in' && (
                                        <Button variant="outline" size="sm" className="text-blue-600 border-blue-600">
                                            Check Out
                                        </Button>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* No reservations message */}
                {reservations.data.length === 0 && (
                    <Card className="text-center py-12">
                        <CardContent>
                            <div className="text-6xl mb-4">📋</div>
                            <h3 className="text-xl font-semibold mb-2">No reservations found</h3>
                            <p className="text-gray-600 mb-6">Start by creating your first reservation.</p>
                            <Link href={route('reservations.create')}>
                                <Button>Create First Reservation</Button>
                            </Link>
                        </CardContent>
                    </Card>
                )}

                {/* Pagination */}
                {reservations.links && (
                    <div className="flex justify-center space-x-2">
                        {reservations.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url || '#'}
                                className={`px-3 py-2 rounded-md ${
                                    link.active 
                                        ? 'bg-blue-500 text-white' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppShell>
    );
}