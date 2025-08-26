import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Guest {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    reservations_count?: number;
    reservations?: Array<{
        id: number;
        status: string;
        check_in_date: string;
        check_out_date: string;
    }>;
}

interface Props {
    guests: {
        data: Guest[];
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

export default function GuestsIndex({ guests }: Props) {
    return (
        <AppShell>
            <Head title="Guest Directory" />
            
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">👥 Guest Directory</h1>
                        <p className="text-gray-600 mt-2">Manage guest profiles and contact information</p>
                    </div>
                    <Link href={route('guests.create')}>
                        <Button>+ Add New Guest</Button>
                    </Link>
                </div>

                {/* Guest Statistics */}
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Stats</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="text-center">
                                <div className="text-3xl font-bold text-blue-600">
                                    {guests.meta?.total || guests.data.length}
                                </div>
                                <div className="text-sm text-gray-600">Total Guests</div>
                            </div>
                            <div className="text-center">
                                <div className="text-3xl font-bold text-green-600">
                                    {guests.data.filter(guest => 
                                        guest.reservations?.some(r => r.status === 'checked_in')
                                    ).length}
                                </div>
                                <div className="text-sm text-gray-600">Currently Staying</div>
                            </div>
                            <div className="text-center">
                                <div className="text-3xl font-bold text-orange-600">
                                    {guests.data.filter(guest => 
                                        guest.reservations?.some(r => r.status === 'confirmed')
                                    ).length}
                                </div>
                                <div className="text-sm text-gray-600">Upcoming Stays</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Guests Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {guests.data.map((guest) => (
                        <Card key={guest.id} className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle className="text-lg">
                                            {guest.first_name} {guest.last_name}
                                        </CardTitle>
                                        <p className="text-sm text-gray-600">{guest.email}</p>
                                        {guest.phone && (
                                            <p className="text-sm text-gray-600">{guest.phone}</p>
                                        )}
                                    </div>
                                    <div className="text-4xl">👤</div>
                                </div>
                            </CardHeader>
                            
                            <CardContent className="space-y-4">
                                {/* Guest Status */}
                                <div>
                                    {guest.reservations?.find(r => r.status === 'checked_in') && (
                                        <Badge className="bg-green-500 text-white">Currently Staying</Badge>
                                    )}
                                    {guest.reservations?.find(r => r.status === 'confirmed') && 
                                     !guest.reservations?.find(r => r.status === 'checked_in') && (
                                        <Badge className="bg-blue-500 text-white">Upcoming Stay</Badge>
                                    )}
                                    {!guest.reservations?.some(r => ['confirmed', 'checked_in'].includes(r.status)) && (
                                        <Badge variant="outline">Past Guest</Badge>
                                    )}
                                </div>

                                {/* Reservation Count */}
                                <div className="text-sm text-gray-600">
                                    {guest.reservations_count || guest.reservations?.length || 0} reservations
                                </div>

                                <div className="flex space-x-2">
                                    <Link href={route('guests.show', guest.id)} className="flex-1">
                                        <Button variant="outline" size="sm" className="w-full">
                                            View Profile
                                        </Button>
                                    </Link>
                                    <Link href={route('guests.edit', guest.id)}>
                                        <Button size="sm">Edit</Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* No guests message */}
                {guests.data.length === 0 && (
                    <Card className="text-center py-12">
                        <CardContent>
                            <div className="text-6xl mb-4">👥</div>
                            <h3 className="text-xl font-semibold mb-2">No guests found</h3>
                            <p className="text-gray-600 mb-6">Start by adding your first guest to the directory.</p>
                            <Link href={route('guests.create')}>
                                <Button>Add First Guest</Button>
                            </Link>
                        </CardContent>
                    </Card>
                )}

                {/* Pagination */}
                {guests.links && (
                    <div className="flex justify-center space-x-2">
                        {guests.links.map((link, index) => (
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